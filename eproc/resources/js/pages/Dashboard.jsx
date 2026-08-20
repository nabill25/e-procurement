import { useState, useEffect } from 'react'
import { fetchAPI } from '@api/client'

export default function Dashboard() {
  const [data, setData] = useState(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    fetchAPI('/api/dashboard')
      .then(res => setData(res.data))
      .catch(err => console.error(err))
      .finally(() => setLoading(false))
  }, [])

  if (loading) return <div className="text-center py-8">Loading...</div>

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50 p-8">
      <div className="max-w-7xl mx-auto">
        <h1 className="text-4xl font-extrabold text-indigo-900 mb-8 tracking-tight drop-shadow-sm">Dasbor Utama</h1>
        
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          {/* Card 1 */}
          <div className="relative overflow-hidden bg-white bg-opacity-60 backdrop-filter backdrop-blur-xl border border-white border-opacity-60 p-6 rounded-3xl shadow-xl transform transition duration-300 hover:scale-105 hover:-translate-y-1 group">
            <div className="absolute top-0 right-0 w-32 h-32 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 group-hover:opacity-40 transition-opacity"></div>
            <h3 className="text-indigo-600 text-sm font-bold uppercase tracking-wider mb-2 relative z-10">Total Vendor Aktif</h3>
            <p className="text-5xl font-black text-indigo-900 relative z-10">142</p>
          </div>

          {/* Card 2 */}
          <div className="relative overflow-hidden bg-white bg-opacity-60 backdrop-filter backdrop-blur-xl border border-white border-opacity-60 p-6 rounded-3xl shadow-xl transform transition duration-300 hover:scale-105 hover:-translate-y-1 group">
             <div className="absolute top-0 right-0 w-32 h-32 bg-purple-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 group-hover:opacity-40 transition-opacity"></div>
            <h3 className="text-indigo-600 text-sm font-bold uppercase tracking-wider mb-2 relative z-10">Tender Berjalan</h3>
            <p className="text-5xl font-black text-indigo-900 relative z-10">12</p>
          </div>

          {/* Card 3 */}
          <div className="relative overflow-hidden bg-white bg-opacity-60 backdrop-filter backdrop-blur-xl border border-white border-opacity-60 p-6 rounded-3xl shadow-xl transform transition duration-300 hover:scale-105 hover:-translate-y-1 group">
             <div className="absolute top-0 right-0 w-32 h-32 bg-pink-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 group-hover:opacity-40 transition-opacity"></div>
            <h3 className="text-indigo-600 text-sm font-bold uppercase tracking-wider mb-2 relative z-10">Transaksi Bulan Ini</h3>
            <p className="text-5xl font-black text-indigo-900 relative z-10">34</p>
          </div>

          {/* Card 4 */}
          <div className="relative overflow-hidden bg-gradient-to-br from-indigo-500 to-purple-600 p-6 rounded-3xl shadow-xl transform transition duration-300 hover:scale-105 hover:-translate-y-1 group border border-indigo-400">
            <h3 className="text-indigo-100 text-sm font-bold uppercase tracking-wider mb-2">Nilai Pengadaan</h3>
            <p className="text-4xl font-black text-white">Rp 2.4M</p>
          </div>
        </div>
      </div>
    </div>
  )
}
