import React, { useEffect, useState } from 'react';
import { useSearchParams } from 'react-router-dom';
import api from '../../api';
import { FileText, CheckCircle, File } from 'lucide-react';

export default function StudentAssignments() {
  const [searchParams] = useSearchParams();
  const [data, setData] = useState(null);
  const [tab, setTab] = useState('value');

  useEffect(() => {
    const course = searchParams.get('course');
    const params = course ? { course } : {};
    api.get('/student/assignments/', { params }).then(r => setData(r.data));
  }, [searchParams]);

  if (!data) return <div className="loading"><div className="spinner" /></div>;

  const grouped = { value: [], boolean: [], static: [] };
  (data.assignments || []).forEach(a => {
    const key = a.activity_type || 'value';
    if (grouped[key]) grouped[key].push(a);
  });

  const tabs = [
    { key: 'value', label: 'Marks', icon: FileText },
    { key: 'boolean', label: 'Attendance', icon: CheckCircle },
    { key: 'static', label: 'Materials', icon: File },
  ];

  return (
    <>
      <div className="page-header">
        <h1>My Assignments</h1>
        <p>Your results across all classes</p>
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
                {tab === 'value' && <><th>Mark</th><th>Total</th><th>%</th></>}
                {tab === 'boolean' && <th>Result</th>}
                {tab === 'static' && <><th>Note</th><th>File</th></>}
              </tr>
            </thead>
            <tbody>
              {grouped[tab].map((a, i) => (
                <tr key={i}>
                  <td>{a.course_name}</td>
                  <td>{a.activity_name}</td>
                  <td>{a.term_name}</td>
                  {tab === 'value' && (
                    <>
                      <td>{a.value ?? '—'}</td>
                      <td>{a.total ?? '—'}</td>
                      <td>{a.value != null && a.total ? `${Math.round(a.value / a.total * 100)}%` : '—'}</td>
                    </>
                  )}
                  {tab === 'boolean' && (
                    <td>
                      <span className={`badge ${a.bool_value ? 'badge-active' : 'badge-inactive'}`}>
                        {a.bool_label || (a.bool_value ? 'Yes' : 'No')}
                      </span>
                    </td>
                  )}
                  {tab === 'static' && (
                    <>
                      <td>{a.note || '—'}</td>
                      <td>{a.file ? <a href={a.file} target="_blank" rel="noreferrer" className="link">Download</a> : '—'}</td>
                    </>
                  )}
                </tr>
              ))}
              {grouped[tab].length === 0 && (
                <tr><td colSpan={tab === 'value' ? 6 : tab === 'static' ? 5 : 4} style={{ textAlign: 'center', color: 'var(--text-muted)' }}>No records</td></tr>
              )}
            </tbody>
          </table>
        </div>
      </div>
    </>
  );
}
