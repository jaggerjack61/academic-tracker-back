import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import api from '../../api';
import { useAuth } from '../../AuthContext';
import { BookOpen } from 'lucide-react';

export default function StudentDashboard() {
  const { user, profile } = useAuth();
  const [data, setData] = useState(null);

  useEffect(() => {
    api.get('/dashboard/student/').then(r => {
      const courses = (r.data.classes || []).map(c => ({
        ...c,
        grade_name: c.grade,
        subject_name: c.subject,
      }));
      setData({ ...r.data, courses });
    });
  }, []);

  if (!data) return <div className="loading"><div className="spinner" /></div>;

  return (
    <>
      <div className="page-header">
        <h1>Welcome, {profile?.first_name}</h1>
        <p>Your academic overview</p>
      </div>
      <div className="page-body">
        <div className="detail-grid">
          <div className="detail-card">
            <h3>Profile</h3>
            <div className="detail-row"><span>Name</span><span>{profile?.first_name} {profile?.last_name}</span></div>
            <div className="detail-row"><span>Email</span><span>{user?.email}</span></div>
            <div className="detail-row"><span>ID Number</span><span>{profile?.id_number || '—'}</span></div>
            <div className="detail-row"><span>Phone</span><span>{profile?.phone_number || '—'}</span></div>
          </div>

          <div className="detail-card">
            <h3><BookOpen size={16} style={{ verticalAlign: -2 }} /> My Classes ({(data.courses || []).length})</h3>
            <div className="table-wrapper">
              <table>
                <thead><tr><th>Class</th><th>Grade</th><th>Subject</th><th></th></tr></thead>
                <tbody>
                  {(data.courses || []).map(c => (
                    <tr key={c.id}>
                      <td>{c.name}</td>
                      <td>{c.grade_name}</td>
                      <td>{c.subject_name}</td>
                      <td><Link to={`/student/assignments?course=${c.id}`} className="btn btn-sm btn-secondary">View</Link></td>
                    </tr>
                  ))}
                  {(data.courses || []).length === 0 && (
                    <tr><td colSpan={4} style={{ textAlign: 'center', color: 'var(--text-muted)' }}>Not enrolled in any class</td></tr>
                  )}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </>
  );
}
