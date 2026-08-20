import { Outlet, Link, useLocation, useNavigate } from 'react-router-dom'
import { fetchAPI } from '@api/client'

export default function Layout() {
  const location = useLocation()
  const navigate = useNavigate()

  const handleLogout = async () => {
    try {
      await fetchAPI('/api/auth/logout', { method: 'POST' })
      navigate('/login')
    } catch (err) {
      console.error(err)
    }
  }

  const menuItems = [
    { path: '/', label: 'Dasbor Utama', icon: '📊' },
    { path: '/permohonan', label: 'Permohonan Pengadaan', icon: '📝' },
    { path: '/paket', label: 'Paket Lelang', icon: '📦' },
    { path: '/rekanan', label: 'Manajemen Rekanan', icon: '🏢' },
    { path: '/bidding', label: 'Bidding & Negosiasi', icon: '🤝' },
    { path: '/evaluasi', label: 'Evaluasi Penawaran', icon: '✅' },
    { path: '/contracting', label: 'Kontrak & SPPBJ', icon: '📑' },
    { path: '/purchasing', label: 'E-Katalog / Purchasing', icon: '🛒' },
    { path: '/master', label: 'Master Data', icon: '⚙️' },
  ]

  return (
    <div className="flex h-screen bg-gray-50 overflow-hidden">
      {/* Glassmorphism Sidebar */}
      <aside className="w-72 bg-indigo-900 text-white flex flex-col shadow-2xl relative">
        <div className="absolute top-0 right-0 w-32 h-32 bg-indigo-600 rounded-full mix-blend-multiply filter blur-2xl opacity-50"></div>
        <div className="absolute bottom-0 left-0 w-32 h-32 bg-blue-600 rounded-full mix-blend-multiply filter blur-2xl opacity-50"></div>
        
        <div className="p-6 relative z-10 flex items-center justify-center border-b border-indigo-700 border-opacity-50">
          <h1 className="text-2xl font-extrabold tracking-widest text-transparent bg-clip-text bg-gradient-to-r from-blue-300 to-white">
            ePROC
          </h1>
        </div>
        
        <nav className="flex-1 overflow-y-auto py-6 px-4 space-y-2 relative z-10 custom-scrollbar">
          {menuItems.map((item) => (
            <Link
              key={item.path}
              to={item.path}
              className={`flex items-center px-4 py-3 rounded-xl transition-all duration-200 group ${
                location.pathname === item.path 
                  ? 'bg-white bg-opacity-10 shadow-inner border border-white border-opacity-20 text-white font-bold' 
                  : 'text-indigo-200 hover:bg-white hover:bg-opacity-5 hover:text-white'
              }`}
            >
              <span className="mr-3 text-lg opacity-80 group-hover:opacity-100">{item.icon}</span>
              <span className="text-sm tracking-wide">{item.label}</span>
            </Link>
          ))}
        </nav>

        <div className="p-4 relative z-10 border-t border-indigo-700 border-opacity-50">
          <button 
            onClick={handleLogout}
            className="w-full flex items-center justify-center px-4 py-3 bg-red-500 bg-opacity-20 hover:bg-opacity-40 border border-red-500 border-opacity-30 text-red-100 rounded-xl transition duration-200 font-semibold text-sm"
          >
            <span className="mr-2">🚪</span> Logout
          </button>
        </div>
      </aside>

      {/* Main Content Area */}
      <main className="flex-1 flex flex-col relative overflow-hidden bg-white">
        <header className="h-16 bg-white shadow-sm flex items-center justify-between px-8 border-b border-gray-100 z-10">
          <h2 className="text-lg font-semibold text-gray-700">Sistem Pengadaan Terintegrasi</h2>
          <div className="flex items-center space-x-4">
             <div className="w-8 h-8 rounded-full bg-indigo-100 border border-indigo-200 flex items-center justify-center text-indigo-700 font-bold">
               U
             </div>
          </div>
        </header>
        <div className="flex-1 overflow-auto bg-gray-50">
          <Outlet />
        </div>
      </main>
    </div>
  )
}
