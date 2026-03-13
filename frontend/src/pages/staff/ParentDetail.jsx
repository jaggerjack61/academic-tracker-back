import React, { useEffect, useState, useCallback } from 'react';
import { useParams, Link } from 'react-router-dom';
import api from '../../api';
import { useToast } from '../../ToastContext';
import { ArrowLeft, Plus, Trash2 } from 'lucide-react';

export default function ParentDetail() {
  const { id } = useParams();
  const [parent, setParent] = useState(null);
  const [showLink, setShowLink] = useState(false);
  const [students, setStudents] = useState([]);
  const [selectedStudent, setSelectedStudent] = useState('');
  const [relationship, setRelationship] = useState('parent');
  const { success, error } = useToast();

  const load = useCallback(() => {
    api.get(`/parents/${id}/`).then(r => {
      const data = r.data;
      const linked = (data.linked_students || []).map(ls => ({
        id: ls.student.id,
        name: ls.student.full_name,
        relationship_id: ls.relationship_id,
      }));
      setParent({ ...data.parent, linked_students: linked });
    }).catch(() => error('Failed to load parent'));
  }, [id, error]);

  useEffect(() => { load(); }, [load]);

  const openLink = () => {
    api.get('/lookup/students/').then(r => {
      const linked = new Set((parent.linked_students || []).map(s => s.id));
      setStudents(r.data.filter(s => !linked.has(s.id)));
      setShowLink(true);
    });
  };

  const linkStudent = async () => {
    if (!selectedStudent) return;
    try {
      await api.post(`/parents/${id}/link-students/`, { student_ids: [selectedStudent] });
      success('Student linked');
      setShowLink(false);
      setSelectedStudent('');
      load();
    } catch { error('Failed to link student'); }
  };

  const unlinkStudent = async (studentId) => {
    try {
      await api.post(`/parents/${id}/unlink-student/`, { student_id: studentId });
      success('Student unlinked');
      load();
    } catch { error('Failed to unlink'); }
  };

  if (!parent) return <div className="loading"><div className="spinner" /></div>;

  return (
    <>
      <div className="page-header">
        <Link to="/app/parents" className="btn btn-ghost"><ArrowLeft size={16}/> Back</Link>
        <h1>{parent.first_name} {parent.last_name}</h1>
      </div>
      <div className="page-body">
        <div className="detail-grid">
          <div className="detail-card">
            <h3>Parent Info</h3>
            <div className="detail-row"><span>Email</span><span>{parent.user?.email}</span></div>
            <div className="detail-row"><span>Phone</span><span>{parent.phone_number || '—'}</span></div>
            <div className="detail-row"><span>Address</span><span>{parent.address || '—'}</span></div>
          </div>

          <div className="detail-card">
            <div className="detail-card-header">
              <h3>Linked Students ({(parent.linked_students || []).length})</h3>
              <div className="detail-card-actions">
                <button className="btn btn-sm btn-primary" onClick={openLink}><Plus size={14}/> Link</button>
              </div>
            </div>
            <div className="table-wrapper">
              <table>
                <thead><tr><th>Student</th><th>Relationship</th><th></th></tr></thead>
                <tbody>
                  {(parent.linked_students || []).map(s => (
                    <tr key={s.id}>
                      <td><Link to={`/app/students/${s.id}`}>{s.name}</Link></td>
                      <td>{s.relationship}</td>
                      <td><button className="btn btn-sm btn-ghost" onClick={() => unlinkStudent(s.id)}><Trash2 size={14}/></button></td>
                    </tr>
                  ))}
                  {(parent.linked_students || []).length === 0 && (
                    <tr><td colSpan={3} className="empty-cell">No linked students</td></tr>
                  )}
                </tbody>
              </table>
            </div>
          </div>
        </div>

        {showLink && (
          <div className="modal-overlay" onClick={() => setShowLink(false)}>
            <div className="modal" onClick={e => e.stopPropagation()}>
              <h2>Link Student</h2>
              <div className="form-group">
                <label>Student</label>
                <select value={selectedStudent} onChange={e => setSelectedStudent(e.target.value)}>
                  <option value="">Choose...</option>
                  {students.map(s => <option key={s.id} value={s.id}>{s.full_name}</option>)}
                </select>
              </div>
              <div className="form-group">
                <label>Relationship</label>
                <select value={relationship} onChange={e => setRelationship(e.target.value)}>
                  <option value="parent">Parent</option>
                  <option value="guardian">Guardian</option>
                  <option value="other">Other</option>
                </select>
              </div>
              <div className="modal-actions">
                <button className="btn btn-ghost" onClick={() => setShowLink(false)}>Cancel</button>
                <button className="btn btn-primary" onClick={linkStudent} disabled={!selectedStudent}>Link</button>
              </div>
            </div>
          </div>
        )}
      </div>
    </>
  );
}
