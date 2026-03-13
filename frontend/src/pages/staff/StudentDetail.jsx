import React, { useEffect, useState, useCallback } from 'react';
import { useParams, Link } from 'react-router-dom';
import api from '../../api';
import { useToast } from '../../ToastContext';
import { ArrowLeft, Plus, Trash2, History } from 'lucide-react';

export default function StudentDetail() {
  const { id } = useParams();
  const [student, setStudent] = useState(null);
  const [courses, setCourses] = useState([]);
  const [available, setAvailable] = useState([]);
  const [showEnroll, setShowEnroll] = useState(false);
  const [selectedCourse, setSelectedCourse] = useState('');
  const { success, error } = useToast();

  const load = useCallback(() => {
    api.get(`/students/${id}/`).then(r => {
      setStudent(r.data.student);
      setCourses((r.data.classes || []).map(c => ({
        id: c.course_id,
        name: c.course_name,
        grade_name: c.grade,
        subject_name: c.subject,
      })));
    }).catch(() => error('Failed to load student'));
  }, [id, error]);

  useEffect(() => { load(); }, [load]);

  const openEnroll = () => {
    api.get('/lookup/courses/').then(r => {
      const enrolled = new Set(courses.map(c => c.id));
      setAvailable(r.data.filter(c => !enrolled.has(c.id)));
      setShowEnroll(true);
    });
  };

  const enroll = async () => {
    if (!selectedCourse) return;
    try {
      await api.post(`/students/${id}/enroll/`, { course_ids: [selectedCourse] });
      success('Student enrolled');
      setShowEnroll(false);
      setSelectedCourse('');
      load();
    } catch { error('Failed to enroll'); }
  };

  const unenroll = async (courseId) => {
    try {
      await api.post(`/students/${id}/unenroll/`, { course_id: courseId });
      success('Student unenrolled');
      load();
    } catch { error('Failed to unenroll'); }
  };

  if (!student) return <div className="loading"><div className="spinner" /></div>;

  return (
    <>
      <div className="page-header">
        <Link to="/app/students" className="btn btn-ghost"><ArrowLeft size={16}/> Back</Link>
        <h1>{student.first_name} {student.last_name}</h1>
      </div>
      <div className="page-body">
        <div className="detail-grid">
          <div className="detail-card">
            <h3>Student Info</h3>
            <div className="detail-row"><span>Email</span><span>{student.user?.email}</span></div>
            <div className="detail-row"><span>ID Number</span><span>{student.id_number || '—'}</span></div>
            <div className="detail-row"><span>Phone</span><span>{student.phone_number || '—'}</span></div>
            <div className="detail-row"><span>Address</span><span>{student.address || '—'}</span></div>
            <div className="detail-row"><span>Status</span><span className={`badge ${student.is_active ? 'badge-active' : 'badge-inactive'}`}>{student.is_active ? 'Active' : 'Inactive'}</span></div>
            <div className="section-link-row">
              <Link to={`/app/students/${id}/history`} className="btn btn-sm btn-secondary"><History size={14}/> Activity History</Link>
            </div>
          </div>
          <div className="detail-card">
            <div className="detail-card-header">
              <h3>Enrolled Classes ({courses.length})</h3>
              <div className="detail-card-actions">
                <button className="btn btn-sm btn-primary" onClick={openEnroll}><Plus size={14}/> Enroll</button>
              </div>
            </div>
            <div className="table-wrapper">
              <table>
                <thead><tr><th>Class</th><th>Grade</th><th>Subject</th><th></th></tr></thead>
                <tbody>
                  {courses.map(c => (
                    <tr key={c.id}>
                      <td><Link to={`/app/classes/${c.id}`}>{c.name}</Link></td>
                      <td>{c.grade_name}</td>
                      <td>{c.subject_name}</td>
                      <td><button className="btn btn-sm btn-ghost" onClick={() => unenroll(c.id)}><Trash2 size={14}/></button></td>
                    </tr>
                  ))}
                  {courses.length === 0 && <tr><td colSpan={4} className="empty-cell">Not enrolled in any class</td></tr>}
                </tbody>
              </table>
            </div>
          </div>
        </div>

        {showEnroll && (
          <div className="modal-overlay" onClick={() => setShowEnroll(false)}>
            <div className="modal" onClick={e => e.stopPropagation()}>
              <h2>Enroll in Class</h2>
              <div className="form-group">
                <label>Select Class</label>
                <select value={selectedCourse} onChange={e => setSelectedCourse(e.target.value)}>
                  <option value="">Choose...</option>
                  {available.map(c => <option key={c.id} value={c.id}>{c.name}</option>)}
                </select>
              </div>
              <div className="modal-actions">
                <button className="btn btn-ghost" onClick={() => setShowEnroll(false)}>Cancel</button>
                <button className="btn btn-primary" onClick={enroll} disabled={!selectedCourse}>Enroll</button>
              </div>
            </div>
          </div>
        )}
      </div>
    </>
  );
}
