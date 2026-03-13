import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import api from '../../api';
import { useAuth } from '../../AuthContext';
import { useToast } from '../../ToastContext';
import { ArrowLeft, Search, MessageCircle } from 'lucide-react';

export default function CollabNewDM() {
  const { role } = useAuth();
  const { error } = useToast();
  const navigate = useNavigate();
  const basePath = role === 'student' ? '/student/collab' : '/app/collab';

  const [search, setSearch] = useState('');
  const [results, setResults] = useState([]);
  const [starting, setStarting] = useState(false);

  const searchUsers = (q) => {
    setSearch(q);
    if (q.trim().length < 2) { setResults([]); return; }
    api.get('/collab/users/', { params: { search: q } })
      .then(r => setResults(r.data))
      .catch(() => {});
  };

  const startDM = async (profileId) => {
    if (starting) return;
    setStarting(true);
    try {
      const r = await api.post('/collab/dm/', { profile_id: profileId });
      navigate(`${basePath}/${r.data.id}`);
    } catch {
      error('Failed to start conversation');
    } finally {
      setStarting(false);
    }
  };

  return (
    <>
      <div className="page-header">
        <button className="btn btn-icon" onClick={() => navigate(basePath)} title="Back">
          <ArrowLeft size={18} />
        </button>
        <h1>New Message</h1>
      </div>
      <div className="page-body">
        <div className="detail-card">
          <div className="form-group">
            <label>Search for a person</label>
            <div className="search-box">
              <Search size={16} />
              <input
                type="text"
                placeholder="Type a name…"
                value={search}
                onChange={e => searchUsers(e.target.value)}
                autoFocus
              />
            </div>
          </div>
          {results.length > 0 && (
            <div className="collab-search-results">
              {results.map(u => (
                <button key={u.id} type="button" className="collab-search-item" onClick={() => startDM(u.id)} disabled={starting}>
                  <div>
                    <strong>{u.full_name}</strong>
                    <span className="badge badge-accent" style={{ marginLeft: '0.5rem' }}>{u.type}</span>
                  </div>
                  <MessageCircle size={16} />
                </button>
              ))}
            </div>
          )}
          {search.trim().length >= 2 && results.length === 0 && (
            <p style={{ color: 'var(--md-sys-color-on-surface-variant)', padding: '1rem 0' }}>No users found.</p>
          )}
        </div>
      </div>
    </>
  );
}
