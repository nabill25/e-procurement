import { createContext, useContext, useState, useCallback, useEffect } from 'react';
import { mockUsers } from '../data/mockData';

const AppContext = createContext(null);
// Di production (Vercel), isi VITE_API_BASE di environment variables supaya menunjuk ke
// backend yang sebenarnya (misal https://nama-app.up.railway.app/api). Kalau kosong, otomatis
// pakai localhost:3001 supaya development lokal tetap jalan tanpa perlu setting apapun.
export const API_BASE = import.meta.env.VITE_API_BASE || 'http://localhost:3001/api';
// SERVER_BASE (tanpa akhiran /api) - dipakai sebagai fallback untuk file lama (lihat
// resolveFileUrl di bawah), bukan lewat endpoint /api.
export const SERVER_BASE = API_BASE.replace(/\/api\/?$/, '');

// ── Helper: ubah nilai file_path/gambar_path/dst dari database jadi URL yang siap dipakai ──
// Sejak file upload dipindah ke Supabase Storage, nilai baru yang tersimpan di database SELALU
// berupa URL lengkap (https://...supabase.co/storage/...) - tinggal dipakai apa adanya. Fungsi
// ini cuma dibutuhkan untuk baris data LAMA (dari sebelum migrasi) yang masih menyimpan path
// relatif ala /uploads/xxx.pdf atau nama file polos tanpa awalan - itu tetap dicoba lewat
// backend lokal (SERVER_BASE) sebagai jaring pengaman, walau di Railway kemungkinan filenya
// sudah tidak ada lagi (baris data lama yang belum ikut dimigrasikan).
export function resolveFileUrl(filePath) {
  if (!filePath) return null;
  if (/^https?:\/\//i.test(filePath)) return filePath; // sudah URL lengkap (Supabase Storage / eksternal lain)
  const withSlash = filePath.startsWith('/') ? filePath : `/uploads/${filePath}`;
  return `${SERVER_BASE}${withSlash}`;
}

// ── Helper: buat headers dengan Authorization JWT (mengikuti alur eProc session) ──
export function getAuthHeaders() {
  const token = localStorage.getItem('dpbj_token');
  return {
    'Content-Type': 'application/json',
    ...(token ? { Authorization: `Bearer ${token}` } : {}),
  };
}

// ── User default (fallback demo jika tidak ada koneksi backend) ──
const DEFAULT_DEMO_USER = mockUsers[0];

// ── Deep-link: buka /verify/KODE langsung ke halaman verifikasi QR ──
// (misalnya saat kode QR di dokumen dipindai lewat kamera HP)
// ── Deep-link: buka /cetak/JENIS/TENDER_ID(/VENDOR_ID) langsung ke halaman cetak dokumen
// resmi (BAPP, Berita Acara Aanwijzing, Pakta Integritas, SPPBJ) - dibuka di tab baru dari
// tombol "Cetak" di detail tender, supaya hasil cetak/PDF bersih tanpa sidebar aplikasi.
function getDeepLinkFromUrl() {
  const verifyMatch = window.location.pathname.match(/^\/verify\/([A-Za-z0-9]+)$/);
  if (verifyMatch) return { page: 'public_qr_verify', code: verifyMatch[1].toUpperCase() };

  const cetakMatch = window.location.pathname.match(/^\/cetak\/([a-z-]+)\/([A-Za-z0-9-]+)(?:\/([A-Za-z0-9_-]+))?$/);
  if (cetakMatch) return { page: 'print_document', code: null, printJenis: cetakMatch[1], printTenderId: cetakMatch[2], printVendorId: cetakMatch[3] || null };

  const resetMatch = window.location.pathname.match(/^\/reset-password\/([A-Za-z0-9]+)$/);
  if (resetMatch) return { page: 'reset_password', code: null, resetToken: resetMatch[1] };

  return { page: 'public_home', code: null };
}
const initialDeepLink = getDeepLinkFromUrl();

export function AppProvider({ children }) {
  const [activePage, setActivePage] = useState(initialDeepLink.page);
  const [qrVerifyCode] = useState(initialDeepLink.code);
  const [printDeepLink] = useState(
    initialDeepLink.printJenis
      ? { jenis: initialDeepLink.printJenis, tenderId: initialDeepLink.printTenderId, vendorId: initialDeepLink.printVendorId }
      : null
  );
  const [resetPasswordToken] = useState(initialDeepLink.resetToken || null);
  const [user, setUser] = useState(null);           // null = belum login
  const [isAuthenticated, setIsAuthenticated] = useState(false);
  const [isAuthLoading, setIsAuthLoading] = useState(true); // cek session awal
  const [availableRoles, setAvailableRoles] = useState([]); // role yang dimiliki akun ini
  const [showRoleSwitcher, setShowRoleSwitcher] = useState(false);

  // State UI
  const [tenders, setTenders] = useState([]);
  const [requests, setRequests] = useState([]);
  const [notifications, setNotifications] = useState([]);
  const [showNewProcurementModal, setShowNewProcurementModal] = useState(false);
  const [showSettingsModal, setShowSettingsModal] = useState(false);
  const [isSidebarOpen, setIsSidebarOpen] = useState(false); // drawer sidebar khusus layar mobile
  const [selectedPengajuan, setSelectedPengajuan] = useState(null);
  const [selectedTender, setSelectedTender] = useState(null);
  const [searchQuery, setSearchQuery] = useState('');
  const [statusFilter, setStatusFilter] = useState('');
  const [refreshTrigger, setRefreshTrigger] = useState(0);

  const triggerRefresh = useCallback(() => setRefreshTrigger(t => t + 1), []);

  const openNewProcurementModal = useCallback(() => setShowNewProcurementModal(true), []);
  const closeNewProcurementModal = useCallback(() => setShowNewProcurementModal(false), []);
  const openSettingsModal = useCallback(() => setShowSettingsModal(true), []);
  const closeSettingsModal = useCallback(() => setShowSettingsModal(false), []);

  // ── Cek session awal (mengikuti alur eProc Auth::me()) ──────────────────────
  useEffect(() => {
    const checkSession = async () => {
      const token = localStorage.getItem('dpbj_token');
      if (!token) {
        setIsAuthLoading(false);
        return;
      }
      try {
        const res = await fetch(`${API_BASE}/auth/me`, {
          headers: { Authorization: `Bearer ${token}` },
        });
        const json = await res.json();
        if (json.success && json.user) {
          // Map role dari backend ke format UI
          const mappedUser = mapBackendUser(json.user);
          setUser(mappedUser);
          setIsAuthenticated(true);
          // Jika halaman adalah public, redirect ke dashboard
          setActivePage(prev => (prev === 'public_home' || !prev ? 'dashboard' : prev));

          // Ambil daftar role yang dimiliki akun ini (untuk tombol "Ganti Role")
          fetch(`${API_BASE}/auth/my-roles`, { headers: { Authorization: `Bearer ${token}` } })
            .then(r => r.json())
            .then(j => { if (j.success) setAvailableRoles(j.data); })
            .catch(() => {});
        } else {
          // Token invalid atau expired — bersihkan
          localStorage.removeItem('dpbj_token');
        }
      } catch {
        // Jika backend tidak konek, tetap di landing page (tidak error)
        localStorage.removeItem('dpbj_token');
      } finally {
        setIsAuthLoading(false);
      }
    };
    checkSession();
  }, []);

  // ── Fungsi login (mengikuti alur eProc Auth::login()) ───────────────────────
  const login = useCallback(async (username, password) => {
    try {
      const res = await fetch(`${API_BASE}/auth/login`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username, password }),
      });
      const json = await res.json();

      if (!json.success) {
        return { success: false, message: json.message || 'Login gagal.' };
      }

      // Simpan JWT ke localStorage
      localStorage.setItem('dpbj_token', json.token);

      // Map dan simpan user di state
      const mappedUser = mapBackendUser(json.user);
      setUser(mappedUser);
      setIsAuthenticated(true);
      setActivePage('dashboard');
      setAvailableRoles(json.available_roles || []);

      // Tambah notifikasi selamat datang
      addNotification('success', 'Login Berhasil', `Selamat datang, ${mappedUser.name}`, 'CheckCircle2', 'text-green-500');

      // Kalau akun ini punya lebih dari satu role, tawarkan untuk pilih role aktif
      // (mengikuti alur eProc: popup pilih role setelah login kalau akun multi-role)
      if ((json.available_roles || []).length > 1) {
        setShowRoleSwitcher(true);
      }

      return { success: true };
    } catch (err) {
      // Fallback demo: jika backend tidak bisa dikoneksi
      const DEMO_USERS = [
        { username: 'admin',  password: 'admin123',  data: mockUsers[0] },
        { username: 'ppk',    password: 'ppk123',    data: mockUsers[1] },
        { username: 'pokja',  password: 'pokja123',  data: mockUsers[2] },
        { username: 'vendor', password: 'vendor123', data: mockUsers[3] },
      ];
      const demoMatch = DEMO_USERS.find(u => u.username === username && u.password === password);
      if (demoMatch) {
        setUser(demoMatch.data);
        setIsAuthenticated(true);
        setActivePage('dashboard');
        return { success: true, demo: true };
      }
      return { success: false, message: 'Tidak dapat terhubung ke server. Coba lagi.' };
    }
  }, []);

  // ── Fungsi logout (mengikuti alur eProc Auth::logout()) ─────────────────────
  const logout = useCallback(async () => {
    const token = localStorage.getItem('dpbj_token');
    if (token) {
      try {
        await fetch(`${API_BASE}/auth/logout`, {
          method: 'POST',
          headers: { Authorization: `Bearer ${token}` },
        });
      } catch { /* tidak blok logout jika server tidak respon */ }
    }
    localStorage.removeItem('dpbj_token');
    setUser(null);
    setIsAuthenticated(false);
    setActivePage('public_home');
    setSelectedPengajuan(null);
    setSelectedTender(null);
  }, []);

  // ── Notifications ────────────────────────────────────────────────────────────
  const addNotification = useCallback((type, title, desc, iconName, color) => {
    const newNotif = {
      id: Date.now() + Math.random(),
      type,
      title,
      desc,
      time: 'Baru saja',
      iconName: iconName || 'Bell',
      color: color || 'text-blue-500',
      read: false,
    };
    setNotifications(prev => [newNotif, ...prev]);
  }, []);

  const markAllAsRead = useCallback(() => {
    setNotifications(prev => prev.map(n => ({ ...n, read: true })));
  }, []);

  const markOneAsRead = useCallback((id) => {
    setNotifications(prev => prev.map(n => (n.id === id ? { ...n, read: true } : n)));
  }, []);

  // ── Ganti role aktif (mengikuti alur eProc excSplitRole) ─────────────────────
  const switchRole = useCallback(async (role_key) => {
    try {
      const res = await fetch(`${API_BASE}/auth/switch-role`, {
        method: 'POST',
        headers: getAuthHeaders(),
        body: JSON.stringify({ role_key }),
      });
      const json = await res.json();
      if (json.success) {
        localStorage.setItem('dpbj_token', json.token);
        setUser(mapBackendUser(json.user));
        setShowRoleSwitcher(false);
        setActivePage('dashboard');
        addNotification('success', 'Role Diganti', json.message, 'CheckCircle2', 'text-green-500');
        return { success: true };
      }
      return { success: false, message: json.message };
    } catch (err) {
      return { success: false, message: 'Terjadi kesalahan saat mengganti role.' };
    }
  }, [addNotification]);

  // ── Tambah pengajuan baru ────────────────────────────────────────────────────
  const addRequest = useCallback(async (requestData) => {
    try {
      const isFormData = requestData instanceof FormData;
      
      const headers = getAuthHeaders();
      if (isFormData) {
        delete headers['Content-Type'];
      }

      const response = await fetch(`${API_BASE}/pengajuan`, {
        method: 'POST',
        headers: headers,
        body: isFormData ? requestData : JSON.stringify(requestData),
      });
      const data = await response.json();
      if (data.success) {
        triggerRefresh();
        addNotification('success', 'Pengajuan Berhasil', `Pengajuan "${requestData.title}" berhasil disimpan.`, 'CheckCircle2', 'text-green-500');
        return { success: true, message: data.message };
      } else {
        throw new Error(data.message);
      }
    } catch (error) {
      console.error('Failed to add request:', error);
      addNotification('error', 'Gagal', 'Terjadi kesalahan saat menyimpan pengajuan.', 'AlertCircle', 'text-red-500');
      throw error;
    }
  }, [addNotification, triggerRefresh]);

  const value = {
    activePage, setActivePage,
    qrVerifyCode,
    printDeepLink,
    resetPasswordToken,
    user, setUser,
    isAuthenticated,
    isAuthLoading,
    login,
    logout,
    availableRoles,
    showRoleSwitcher, setShowRoleSwitcher,
    switchRole,
    tenders, setTenders,
    requests, setRequests,
    notifications, addNotification, markAllAsRead, markOneAsRead,
    showNewProcurementModal, openNewProcurementModal, closeNewProcurementModal,
    showSettingsModal, openSettingsModal, closeSettingsModal,
    isSidebarOpen, setIsSidebarOpen,
    selectedPengajuan, setSelectedPengajuan,
    selectedTender, setSelectedTender,
    searchQuery, setSearchQuery,
    statusFilter, setStatusFilter,
    addRequest,
    refreshTrigger, triggerRefresh,
  };

  return <AppContext.Provider value={value}>{children}</AppContext.Provider>;
}

export const useApp = () => {
  const ctx = useContext(AppContext);
  if (!ctx) throw new Error('useApp must be used within AppProvider');
  return ctx;
};

// ── Helper: map user dari backend ke format UI ────────────────────────────────
// Mengikuti field dari eProc: user_login, user_nama, user_type_id
function mapBackendUser(backendUser) {
  const roleLabels = {
    admin:  'Admin DPBJ (Superuser)',
    ppk:    'PPK (Pejabat Pembuat Komitmen)',
    pokja:  'Ketua Pokja Pemilihan',
    vendor: 'Vendor / Penyedia',
  };
  return {
    id:           backendUser.id,
    name:         backendUser.nama || backendUser.full_name || backendUser.username,
    username:     backendUser.username || '',
    role:         backendUser.role || 'ppk',
    roleLabel:    roleLabels[backendUser.role] || backendUser.role,
    unit:         backendUser.unit || '',
    email:        backendUser.email || '',
    avatar:       null,
    nip:          backendUser.nip || '',
    notifications: 0,
  };
}
