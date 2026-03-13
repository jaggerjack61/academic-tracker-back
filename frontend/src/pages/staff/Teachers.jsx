import React, { useEffect, useState, useCallback } from 'react';
import { Link } from 'react-router-dom';
import api from '../../api';
import { useToast } from '../../ToastContext';
import { Search } from 'lucide-react';

export default function Teachers() {
  const [teachers, setTeachers] = useState([]);
  const [search, setSearch] = useState('');
  const { error } = useToast();

  const load = useCallback(() => {
    api.get('/teachers/', { params: { search } })
      .then(r => setTeachers(r.data.results || r.data))
      .catch(() => error('Failed to load teachers'));
  }, [search, error]);

  useEffect(() => { load(); }, [load]);

  return (
    <>
      <div className="page-header">
        <h1>Teachers</h1>
        <p>{teachers.length} records</p>
      </div>
      <div className="page-body">
        <div className="toolbar">
          <div className="search-box">
            <Search size={16} />
            <input placeholder="Search teachers..." value={search} onChange={e => setSearch(e.target.value)} />
          </div>
        </div>

        <div className="table-wrapper">
          <table>
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              {teachers.map(t => (
                <tr key={t.id}>
                  <td><Link to={`/app/teachers/${t.id}`}>{t.first_name} {t.last_name}</Link></td>
                  <td>{t.user?.email}</td>
                  <td>{t.phone_number || '—'}</td>
                  <td><span className={`badge ${t.is_active ? 'badge-active' : 'badge-inactive'}`}>{t.is_active ? 'Active' : 'Inactive'}</span></td>
                </tr>
              ))}
              {teachers.length === 0 && (
                <tr><td colSpan={4} style={{ textAlign: 'center', color: 'var(--text-muted)' }}>No teachers found</td></tr>
              )}
            </tbody>
          </table>
        </div>
      </div>
    </>
  );
}
