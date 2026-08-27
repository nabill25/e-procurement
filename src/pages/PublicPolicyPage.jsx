import { useState, useEffect } from 'react';
import DOMPurify from 'dompurify';
import { FileText, Home } from 'lucide-react';
import { API_BASE } from '../context/AppContext';

function Breadcrumb({ onHome }) {
  return (
    <nav className="flex items-center gap-2 text-xs text-muted mb-4">
      <button onClick={onHome} className="text-dpbj-gold hover:underline flex items-center gap-1">
        <Home size={11} /> Home
      </button>
      <span>/</span>
      <span className="text-dpbj-navy font-medium">Kebijakan</span>
    </nav>
  );
}

export default function PublicPolicyPage({ onNavigateHome }) {
  const [policies, setPolicies] = useState([]);
  const [activeId, setActiveId] = useState(null);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    fetch(`${API_BASE}/cms/policies`)
      .then(res => res.json())
      .then(json => {
        if (json.success) {
          setPolicies(json.data);
          if (json.data.length) setActiveId(json.data[0].id);
        }
      })
      .catch(() => {})
      .finally(() => setIsLoading(false));
  }, []);

  const active = policies.find(p => p.id === activeId);

  return (
    <div className="animate-fade-in space-y-4">
      <Breadcrumb onHome={onNavigateHome} />

      <div className="bg-white rounded-xl border border-border shadow-card p-6">
        <div className="flex items-center gap-2 mb-6 pb-4 border-b border-border">
          <FileText size={18} className="text-dpbj-navy" />
          <h2 className="font-bold text-dpbj-navy text-base">Kebijakan</h2>
        </div>

        {isLoading ? (
          <p className="text-sm text-muted text-center py-10">Memuat...</p>
        ) : policies.length === 0 ? (
          <p className="text-sm text-muted text-center py-10">Belum ada kebijakan yang dipublikasikan.</p>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div className="md:col-span-1 space-y-1 stagger-list">
              {policies.map(p => (
                <button
                  key={p.id}
                  onClick={() => setActiveId(p.id)}
                  className={`stagger-item w-full text-left px-3 py-2 text-sm rounded-lg transition-colors ${activeId === p.id ? 'bg-dpbj-gold-faint text-dpbj-navy font-bold' : 'text-muted hover:bg-surface'}`}
                >
                  {p.title}
                </button>
              ))}
            </div>
            <div className="md:col-span-3">
              {active && (
                <div>
                  <h3 className="font-bold text-dpbj-navy text-lg mb-4">{active.title}</h3>
                  <div className="text-sm text-dpbj-navy leading-relaxed" dangerouslySetInnerHTML={{ __html: DOMPurify.sanitize(active.content) }} />
                </div>
              )}
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
