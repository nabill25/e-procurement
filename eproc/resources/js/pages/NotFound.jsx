import { Link } from 'react-router-dom'

export default function NotFound() {
  return (
    <div className="flex flex-col items-center justify-center min-h-screen">
      <h1 className="text-6xl font-bold text-primary mb-4">404</h1>
      <p className="text-xl text-gray-600 mb-8">Page not found</p>
      <Link to="/" className="px-6 py-3 bg-primary text-white rounded hover:bg-blue-700">
        Back to Dashboard
      </Link>
    </div>
  )
}
