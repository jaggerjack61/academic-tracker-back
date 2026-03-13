import React, { useEffect, useState, useCallback } from 'react';
import { Link } from 'react-router-dom';
import api from '../../api';
import { useToast } from '../../ToastContext';
import { Search } from 'lucide-react';

export default function Parents() {
  const [parents, setParents] = useState([]);
  const [search, setSearch] = useState('');
  const { error } = useToast();

  const load = useCallback(() => {
    api.get('/parents/', { params: { search } })
      .then(r => setParents(r.data.results || r.data))
      .catch(() => error('Failed to load parents'));
  }, [search, error]);

  useEffect(() => { load(); }, [load]);

  return (
    <>
      <div className="page-header">
        <h1>Parents / Guardians</h1>
        <p>{parents.length} records</p>
      </div>
      <div className="page-body">
        <div className="toolbar">
          <div className="search-box">
            <Search size={16} />
            <input placeholder="Search parents..." value={search} onChange={e => setSearch(e.target.value)} />
          </div>
        </div>

        <div className="table-wrapper">
          <table>
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Linked Students</th>
              </tr>
            </thead>
            <tbody>
              {parents.map(p => (
                <tr key={p.id}>
                  <td><Link to={`/app/parents/${p.id}`}>{p.first_name} {p.last_name}</Link></td>
                  <td>{p.user?.email}</td>
                  <td>{p.phone_number || '—'}</td>
                  <td>{p.student_count ?? 0}</td>
                </tr>
              ))}
              {parents.length === 0 && (
                <tr><td colSpan={4} style={{ textAlign: 'center', color: 'var(--text-muted)' }}>No parents found</td></tr>
              )}
            </tbody>
          </table>
        </div>
      </div>
    </>
  );
}
