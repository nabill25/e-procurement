import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom'
import Layout from '@components/Layout'
import Dashboard from '@pages/Dashboard'
import Login from '@pages/Login'
import PermohonanPaket from '@pages/PermohonanPaket'
import PaketPengadaan from '@pages/PaketPengadaan'
import Rekanan from '@pages/Rekanan'
import Bidding from '@pages/Bidding'
import Evaluasi from '@pages/Evaluasi'
import Contracting from '@pages/Contracting'
import Purchasing from '@pages/Purchasing'
import MasterData from '@pages/MasterData'
import NotFound from '@pages/NotFound'

function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/login" element={<Login />} />
        <Route element={<Layout />}>
          <Route path="/" element={<Dashboard />} />
          <Route path="/permohonan" element={<PermohonanPaket />} />
          <Route path="/paket" element={<PaketPengadaan />} />
          <Route path="/rekanan" element={<Rekanan />} />
          <Route path="/bidding" element={<Bidding />} />
          <Route path="/evaluasi" element={<Evaluasi />} />
          <Route path="/contracting" element={<Contracting />} />
          <Route path="/purchasing" element={<Purchasing />} />
          <Route path="/master" element={<MasterData />} />
          <Route path="*" element={<NotFound />} />
        </Route>
      </Routes>
    </BrowserRouter>
  )
}

export default App
