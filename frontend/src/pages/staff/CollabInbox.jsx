import React, { useEffect, useState, useCallback } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import api from '../../api';
import { useAuth } from '../../AuthContext';
import { useToast } from '../../ToastContext';
import { MessageCircle, Users, Search, Plus, BookOpen } from 'lucide-react';

export default function CollabInbox() {
  const { role } = useAuth();
  const { error } = useToast();
  const navigate = useNavigate();
  const [groups, setGroups] = useState([]);
  const [total, setTotal] = useState(0);
  const [page, setPage] = useState(1);
  const [search, setSearch] = useState('');
  const [loading, setLoading] = useState(true);
  const basePath = role === 'student' ? '/student/collab' : '/app/collab';

  const load = useCallback(() => {
    setLoading(true);
    api.get('/collab/groups/', { params: { page, search, page_size: 30 } })
      .then(r => {
        setGroups(r.data.results);
        setTotal(r.data.total);
      })
      .catch(() => error('Failed to load conversations'))
      .finally(() => setLoading(false));
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [page, search]);

  useEffect(() => { load(); }, [load]);

  const formatTime = (iso) => {
    if (!iso) return '';
    const d = new Date(iso);
    const now = new Date();
    const diffMs = now - d;
    const diffMins = Math.floor(diffMs / 60000);
    if (diffMins < 1) return 'Just now';
    if (diffMins < 60) return `${diffMins}m ago`;
    const diffHrs = Math.floor(diffMins / 60);
    if (diffHrs < 24) return `${diffHrs}h ago`;
    return d.toLocaleDateString();
  };

  const totalPages = Math.ceil(total / 30);

  return (
    <>
      <div className="page-header">
        <h1>Messages</h1>
        <p>{total} conversation{total !== 1 ? 's' : ''}</p>
      </div>
      <div className="page-body">
        <div className="toolbar">
          <div className="search-box">
            <Search size={16} />
            <input
              type="text"
              placeholder="Search conversations…"
              value={search}
              onChange={e => { setSearch(e.target.value); setPage(1); }}
            />
          </div>
          <div className="toolbar-actions">
            <button className="btn btn-secondary" onClick={() => navigate(`${basePath}/new-dm`)}>
              <MessageCircle size={16} /> New Message
            </button>
            {role !== 'student' && (
              <button className="btn btn-primary" onClick={() => navigate(`${basePath}/new-group`)}>
                <Plus size={16} /> New Group
              </button>
            )}
          </div>
        </div>

        {loading ? (
          <div className="loading"><div className="spinner" /></div>
        ) : groups.length === 0 ? (
          <div className="empty-state">
            <MessageCircle size={48} />
            <h3>No conversations yet</h3>
            <p>Start a new message or create a group to begin chatting.</p>
          </div>
        ) : (
          <div className="collab-list">
            {groups.map(g => (
              <Link key={g.id} to={`${basePath}/${g.id}`} className="collab-card">
                <div className="collab-card-icon">
                  {g.is_class_group ? <BookOpen size={20} /> : g.member_count > 2 ? <Users size={20} /> : <MessageCircle size={20} />}
                </div>
                <div className="collab-card-body">
                  <div className="collab-card-top">
                    <strong className="collab-card-name">{g.name}</strong>
                    {g.last_message && (
                      <span className="collab-card-time">{formatTime(g.last_message.created_at)}</span>
                    )}
                  </div>
                  <div className="collab-card-meta">
                    {g.is_class_group && <span className="badge badge-accent">Class</span>}
                    <span className="collab-card-members">{g.member_count} member{g.member_count !== 1 ? 's' : ''}</span>
                  </div>
                  {g.last_message && (
                    <p className="collab-card-preview">
                      <strong>{g.last_message.sender_name}:</strong> {g.last_message.content}
                    </p>
                  )}
                </div>
              </Link>
            ))}
          </div>
        )}

        {totalPages > 1 && (
          <div className="pagination">
            <button className="btn btn-secondary" disabled={page <= 1} onClick={() => setPage(p => p - 1)}>Previous</button>
            <span>Page {page} of {totalPages}</span>
            <button className="btn btn-secondary" disabled={page >= totalPages} onClick={() => setPage(p => p + 1)}>Next</button>
          </div>
        )}
      </div>
    </>
  );
}
