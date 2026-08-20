import React from 'react'

export default function PlaceholderPage({ title, description, icon }) {
  return (
    <div className="min-h-full p-8 flex flex-col items-center justify-center">
      <div className="bg-white p-10 rounded-3xl shadow-xl border border-gray-100 max-w-2xl w-full text-center relative overflow-hidden">
        {/* Decorative background blobs */}
        <div className="absolute -top-10 -right-10 w-40 h-40 bg-indigo-100 rounded-full mix-blend-multiply filter blur-xl opacity-70"></div>
        <div className="absolute -bottom-10 -left-10 w-40 h-40 bg-blue-100 rounded-full mix-blend-multiply filter blur-xl opacity-70"></div>
        
        <div className="relative z-10">
          <div className="text-6xl mb-6">{icon}</div>
          <h1 className="text-3xl font-bold text-gray-800 mb-4">{title}</h1>
          <p className="text-gray-500 text-lg mb-8 leading-relaxed">
            {description}
          </p>
          
          <div className="inline-flex items-center px-6 py-3 bg-indigo-50 text-indigo-700 rounded-full font-semibold text-sm">
            <span className="w-2 h-2 rounded-full bg-indigo-500 mr-2 animate-pulse"></span>
            Sedang dalam tahap pengembangan
          </div>
        </div>
      </div>
    </div>
  )
}
