import React, { useEffect, useState } from 'react';
import { useParams, Link } from 'react-router-dom';
import api from '../../api';
import { ArrowLeft, FileText, CheckCircle, File } from 'lucide-react';

export default function StudentHistory() {
  const { id } = useParams();
  const [data, setData] = useState(null);
  const [tab, setTab] = useState('value');

  useEffect(() => {
    api.get(`/students/${id}/activity-history/`).then(r => setData(r.data));
  }, [id]);

  if (!data) return <div className="loading"><div className="spinner" /></div>;

  const grouped = { value: [], boolean: [], static: [] };
  (data.history || []).forEach(h => {
    const key = h.activity_type || 'value';
    if (grouped[key]) grouped[key].push(h);
  });

  const tabs = [
    { key: 'value', label: 'Marks', icon: FileText },
    { key: 'boolean', label: 'Attendance', icon: CheckCircle },
    { key: 'static', label: 'Materials', icon: File },
  ];

  return (
    <>
      <div className="page-header">
        <Link to={`/app/students/${id}`} className="btn btn-ghost"><ArrowLeft size={16}/> Back</Link>
        <h1>Activity History — {data.student_name}</h1>
      </div>
      <div className="page-body">
        <div className="tabs">
          {tabs.map(t => (
            <button key={t.key} className={`tab ${tab === t.key ? 'active' : ''}`} onClick={() => setTab(t.key)}>
              <t.icon size={14}/> {t.label} ({grouped[t.key].length})
            </button>
          ))}
        </div>

        <div className="table-wrapper">
          <table>
            <thead>
              <tr>
                <th>Class</th>
                <th>Activity</th>
                <th>Term</th>
                {tab === 'value' && <><th>Value</th><th>Total</th><th>%</th></>}
                {tab === 'boolean' && <th>Result</th>}
                {tab === 'static' && <th>Info</th>}
              </tr>
            </thead>
            <tbody>
              {grouped[tab].map((h, i) => (
                <tr key={i}>
                  <td>{h.course_name}</td>
                  <td>{h.activity_name}</td>
                  <td>{h.term_name}</td>
                  {tab === 'value' && (
                    <>
                      <td>{h.value ?? '—'}</td>
                      <td>{h.total ?? '—'}</td>
                      <td>{h.value != null && h.total ? `${Math.round(h.value / h.total * 100)}%` : '—'}</td>
                    </>
                  )}
                  {tab === 'boolean' && (
                    <td>
                      <span className={`badge ${h.bool_value ? 'badge-active' : 'badge-inactive'}`}>
                        {h.bool_label || (h.bool_value ? 'Yes' : 'No')}
                      </span>
                    </td>
                  )}
                  {tab === 'static' && <td>{h.note || '—'}</td>}
                </tr>
              ))}
              {grouped[tab].length === 0 && (
                <tr><td colSpan={tab === 'value' ? 6 : 4} style={{ textAlign: 'center', color: 'var(--text-muted)' }}>No records</td></tr>
              )}
            </tbody>
          </table>
        </div>
      </div>
    </>
  );
}
