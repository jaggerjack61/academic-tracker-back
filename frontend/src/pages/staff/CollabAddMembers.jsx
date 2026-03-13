import React, { useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import api from '../../api';
import { useAuth } from '../../AuthContext';
import { useToast } from '../../ToastContext';
import { ArrowLeft, Search, Plus, X } from 'lucide-react';

export default function CollabAddMembers() {
  const { id } = useParams();
  const { role } = useAuth();
  const { error, success } = useToast();
  const navigate = useNavigate();
  const basePath = role === 'student' ? '/student/collab' : '/app/collab';

  const [search, setSearch] = useState('');
  const [results, setResults] = useState([]);
  const [selected, setSelected] = useState([]);
  const [saving, setSaving] = useState(false);

  const searchUsers = (q) => {
    setSearch(q);
    if (q.trim().length < 2) { setResults([]); return; }
    api.get('/collab/users/', { params: { search: q } })
      .then(r => setResults(r.data.filter(u => !selected.some(s => s.id === u.id))))
      .catch(() => {});
  };

  const addMember = (user) => {
    setSelected(prev => [...prev, user]);
    setResults(prev => prev.filter(u => u.id !== user.id));
    setSearch('');
  };

  const removeMember = (uid) => {
    setSelected(prev => prev.filter(u => u.id !== uid));
  };

  const handleSubmit = async () => {
    if (selected.length === 0) { error('Select at least one member'); return; }
    setSaving(true);
    try {
      await api.post(`/collab/groups/${id}/add-members/`, { member_ids: selected.map(u => u.id) });
      success(`${selected.length} member${selected.length > 1 ? 's' : ''} added`);
      navigate(`${basePath}/${id}`);
    } catch {
      error('Failed to add members');
    } finally {
      setSaving(false);
    }
  };

  return (
    <>
      <div className="page-header">
        <button className="btn btn-icon" onClick={() => navigate(`${basePath}/${id}`)} title="Back">
          <ArrowLeft size={18} />
        </button>
        <h1>Add Members</h1>
      </div>
      <div className="page-body">
        <div className="detail-card">
          <div className="form-group">
            <label>Search people</label>
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
            {results.length > 0 && (
              <div className="collab-search-results">
                {results.map(u => (
                  <button key={u.id} type="button" className="collab-search-item" onClick={() => addMember(u)}>
                    <span>{u.full_name}</span>
                    <span className="badge badge-accent">{u.type}</span>
                    <Plus size={14} />
                  </button>
                ))}
              </div>
            )}
          </div>

          {selected.length > 0 && (
            <div className="collab-selected-members">
              {selected.map(u => (
                <span key={u.id} className="collab-member-chip">
                  {u.full_name}
                  <button type="button" onClick={() => removeMember(u.id)}><X size={12} /></button>
                </span>
              ))}
            </div>
          )}

          <div className="form-actions">
            <button className="btn btn-secondary" onClick={() => navigate(`${basePath}/${id}`)}>Cancel</button>
            <button className="btn btn-primary" onClick={handleSubmit} disabled={saving || selected.length === 0}>
              {saving ? 'Adding…' : `Add ${selected.length} Member${selected.length !== 1 ? 's' : ''}`}
            </button>
          </div>
        </div>
      </div>
    </>
  );
}
