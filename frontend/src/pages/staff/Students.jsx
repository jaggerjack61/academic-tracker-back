import React, { useEffect, useState, useCallback } from 'react';
import { Link } from 'react-router-dom';
import api from '../../api';
import { useToast } from '../../ToastContext';
import { Search, ChevronLeft, ChevronRight, Power } from 'lucide-react';

export default function Students() {
  const [students, setStudents] = useState([]);
  const [page, setPage] = useState(1);
  const [total, setTotal] = useState(0);
  const [search, setSearch] = useState('');
  const { success, error } = useToast();
  const [pageSize, setPageSize] = useState(20);

  const load = useCallback(() => {
    api.get('/students/', { params: { page, search, page_size: pageSize } })
      .then(r => { setStudents(r.data.results); setTotal(r.data.total || r.data.count || 0); })
      .catch(() => error('Failed to load students'));
  }, [page, search, pageSize, error]);

  useEffect(() => { load(); }, [load]);
  useEffect(() => { setPage(1); }, [search]);

  const toggleStatus = async (id) => {
    try {
      await api.post(`/students/${id}/toggle-status/`);
      success('Status updated');
      load();
    } catch { error('Failed to toggle status'); }
  };

  const totalPages = Math.ceil(total / pageSize);

  return (
    <>
      <div className="page-header">
        <h1>Students</h1>
        <p>{total} total students</p>
      </div>
      <div className="page-body">
        <div className="toolbar">
          <div className="search-box">
            <Search size={16} />
            <input placeholder="Search students..." value={search} onChange={e => setSearch(e.target.value)} />
          </div>
        </div>

        <div className="table-wrapper">
          <table>
            <thead>
              <tr>
                <th>Name</th>
                <th>ID Number</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              {students.map(s => (
                <tr key={s.id}>
                  <td><Link to={`/app/students/${s.id}`}>{s.first_name} {s.last_name}</Link></td>
                  <td>{s.id_number}</td>
                  <td>{s.phone_number || '—'}</td>
                  <td><span className={`badge ${s.is_active ? 'badge-active' : 'badge-inactive'}`}>{s.is_active ? 'Active' : 'Inactive'}</span></td>
                  <td>
                    <button className="btn btn-sm btn-ghost" onClick={() => toggleStatus(s.id)} title="Toggle status">
                      <Power size={14} />
                    </button>
                  </td>
                </tr>
              ))}
              {students.length === 0 && (
                <tr><td colSpan={5} style={{ textAlign: 'center', color: 'var(--text-muted)' }}>No students found</td></tr>
              )}
            </tbody>
          </table>
        </div>

        {totalPages > 1 && (
          <div className="pagination">
            <button className="btn btn-sm btn-ghost" disabled={page <= 1} onClick={() => setPage(p => p - 1)}><ChevronLeft size={16} /></button>
            <span>Page {page} of {totalPages}</span>
            <button className="btn btn-sm btn-ghost" disabled={page >= totalPages} onClick={() => setPage(p => p + 1)}><ChevronRight size={16} /></button>
            <select value={pageSize} onChange={e => { setPageSize(Number(e.target.value)); setPage(1); }} style={{ marginLeft: 'auto' }}>
              {[10, 20, 50, 100].map(n => <option key={n} value={n}>{n} / page</option>)}
            </select>
          </div>
        )}
      </div>
    </>
  );
}
