import React, { useEffect, useState, useCallback } from 'react';
import { useParams, Link } from 'react-router-dom';
import api from '../../api';
import { useToast } from '../../ToastContext';
import LoadingButton from '../../components/LoadingButton';
import { ArrowLeft, Save } from 'lucide-react';

export default function ActivityLog() {
  const { classId, activityId } = useParams();
  const [activity, setActivity] = useState(null);
  const [entries, setEntries] = useState([]);
  const [saving, setSaving] = useState(false);
  const { success, error } = useToast();

  const load = useCallback(() => {
    api.get(`/activities/${activityId}/`).then(r => {
      const act = r.data.activity;
      const atData = act.activity_type_data || {};
      setActivity({ ...act, type_kind: atData.type, true_label: atData.true_value, false_label: atData.false_value });
      setEntries((r.data.logs || []).map(e => ({
        ...e,
        // Normalize score into editable fields
        value: e.score,
        bool_value: e.score === 2,
      })));
    }).catch(() => error('Failed to load activity'));
  }, [activityId, error]);

  useEffect(() => { load(); }, [load]);

  const updateEntry = (idx, field, val) => {
    setEntries(prev => {
      const updated = [...prev];
      updated[idx] = { ...updated[idx], [field]: val };
      return updated;
    });
  };

  const saveAll = async () => {
    setSaving(true);
    try {
      const payload = entries.map(e => {
        const item = { student_id: e.student_id };
        if (activity.type_kind === 'value') {
          item.score = e.value === '' || e.value == null ? null : Number(e.value);
        } else if (activity.type_kind === 'boolean') {
          item.checked = !!e.bool_value;
        }
        return item;
      });
      await api.post(`/activities/${activityId}/log/`, { entries: payload });
      success('All entries saved');
      load();
    } catch {
      error('Failed to save entries');
    }
    setSaving(false);
  };

  if (!activity) return <div className="loading"><div className="spinner" /></div>;

  return (
    <>
      <div className="page-header">
        <Link to={`/app/classes/${classId}/activities`} className="btn btn-ghost"><ArrowLeft size={16}/> Back</Link>
        <h1>{activity.name}</h1>
        <p>{activity.activity_type_data?.name || ''} — {activity.course_name || ''}</p>
      </div>
      <div className="page-body">
        <div className="toolbar" style={{ justifyContent: 'flex-end' }}>
          <LoadingButton className="btn btn-primary" onClick={saveAll} loading={saving} loadingText="Saving...">
            <Save size={16}/> Save All
          </LoadingButton>
        </div>

        <div className="table-wrapper">
          <table>
            <thead>
              <tr>
                <th>Student</th>
                {activity.type_kind === 'value' && (
                  <>
                    <th>Mark</th>
                    <th>/ {activity.total}</th>
                    <th>%</th>
                  </>
                )}
                {activity.type_kind === 'boolean' && (
                  <th>{activity.true_label || 'Yes'} / {activity.false_label || 'No'}</th>
                )}
              </tr>
            </thead>
            <tbody>
              {entries.map((e, i) => (
                <tr key={e.id}>
                  <td>{e.student_name}</td>
                  {activity.type_kind === 'value' && (
                    <>
                      <td>
                        <input
                          type="number"
                          value={e.value ?? ''}
                          onChange={ev => updateEntry(i, 'value', ev.target.value)}
                          style={{ width: 80 }}
                          min={0}
                          max={activity.total || undefined}
                        />
                      </td>
                      <td style={{ color: 'var(--text-muted)' }}>{activity.total}</td>
                      <td>{e.value != null && e.value !== '' && activity.total ? `${Math.round(Number(e.value) / activity.total * 100)}%` : '—'}</td>
                    </>
                  )}
                  {activity.type_kind === 'boolean' && (
                    <td>
                      <label style={{ display: 'flex', alignItems: 'center', gap: 8, cursor: 'pointer' }}>
                        <input
                          type="checkbox"
                          checked={e.bool_value || false}
                          onChange={ev => updateEntry(i, 'bool_value', ev.target.checked)}
                        />
                        <span className={`badge ${e.bool_value ? 'badge-active' : 'badge-inactive'}`}>
                          {e.bool_value ? (activity.true_label || 'Yes') : (activity.false_label || 'No')}
                        </span>
                      </label>
                    </td>
                  )}
                </tr>
              ))}
              {entries.length === 0 && (
                <tr><td colSpan={activity.type_kind === 'value' ? 4 : 2} style={{ textAlign: 'center', color: 'var(--text-muted)' }}>No students enrolled</td></tr>
              )}
            </tbody>
          </table>
        </div>
      </div>
    </>
  );
}
