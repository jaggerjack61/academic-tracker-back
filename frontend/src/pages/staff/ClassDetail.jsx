import React, { useEffect, useState, useCallback } from 'react';
import { useParams, Link } from 'react-router-dom';
import api from '../../api';
import { useToast } from '../../ToastContext';
import { ArrowLeft, Plus, Trash2, Copy, ArrowRightLeft, BookOpen } from 'lucide-react';

export default function ClassDetail() {
  const { id } = useParams();
  const [cls, setCls] = useState(null);
  const [roster, setRoster] = useState([]);
  const [showEnroll, setShowEnroll] = useState(false);
  const [showCopy, setShowCopy] = useState(false);
  const [showMove, setShowMove] = useState(false);
  const [available, setAvailable] = useState([]);
  const [targetCourses, setTargetCourses] = useState([]);
  const [selectedStudent, setSelectedStudent] = useState('');
  const [targetCourse, setTargetCourse] = useState('');
  const { success, error } = useToast();

  const load = useCallback(() => {
    api.get(`/classes/${id}/`).then(r => {
      const data = r.data;
      setCls({ ...data.course, teacher: data.teacher });
      setRoster((data.students || []).map(s => ({
        id: s.student.id,
        name: s.student.full_name,
        id_number: s.student.id_number,
      })));
    }).catch(() => error('Failed to load class'));
  }, [id, error]);

  useEffect(() => { load(); }, [load]);

  const openEnroll = () => {
    api.get('/lookup/students/').then(r => {
      const enrolled = new Set(roster.map(s => s.id));
      setAvailable(r.data.filter(s => !enrolled.has(s.id)));
      setShowEnroll(true);
    });
  };

  const enroll = async () => {
    if (!selectedStudent) return;
    try {
      await api.post(`/classes/${id}/enroll/`, { student_ids: [selectedStudent] });
      success('Student enrolled');
      setShowEnroll(false);
      setSelectedStudent('');
      load();
    } catch { error('Failed to enroll'); }
  };

  const unenroll = async (studentId) => {
    try {
      await api.post(`/classes/${id}/unenroll/`, { student_id: studentId });
      success('Student removed');
      load();
    } catch { error('Failed to unenroll'); }
  };

  const openCopyMove = (mode) => {
    api.get('/lookup/courses/').then(r => {
      setTargetCourses(r.data.filter(c => c.id !== parseInt(id)));
      setTargetCourse('');
      if (mode === 'copy') setShowCopy(true);
      else setShowMove(true);
    });
  };

  const copyRoster = async () => {
    try {
      await api.post(`/classes/${id}/copy-roster/`, { destination_course_ids: [targetCourse] });
      success('Roster copied');
      setShowCopy(false);
    } catch { error('Failed to copy roster'); }
  };

  const moveRoster = async () => {
    try {
      await api.post(`/classes/${id}/move-roster/`, { destination_course_ids: [targetCourse] });
      success('Roster moved');
      setShowMove(false);
      load();
    } catch { error('Failed to move roster'); }
  };

  if (!cls) return <div className="loading"><div className="spinner" /></div>;

  return (
    <>
      <div className="page-header">
        <Link to="/app/classes" className="btn btn-ghost"><ArrowLeft size={16}/> Back</Link>
        <h1>{cls.name}</h1>
      </div>
      <div className="page-body">
        <div className="detail-grid">
          <div className="detail-card">
            <h3>Class Info</h3>
            <div className="detail-row"><span>Grade</span><span>{cls.grade_name}</span></div>
            <div className="detail-row"><span>Subject</span><span>{cls.subject_name}</span></div>
            <div className="detail-row"><span>Teacher</span><span>{cls.teacher?.full_name || '—'}</span></div>
            <div className="detail-row"><span>Status</span><span className={`badge ${cls.is_active ? 'badge-active' : 'badge-inactive'}`}>{cls.is_active ? 'Active' : 'Inactive'}</span></div>
            <div className="section-link-row">
              <Link to={`/app/classes/${id}/activities`} className="btn btn-sm btn-secondary"><BookOpen size={14}/> Activities</Link>
            </div>
          </div>

          <div className="detail-card">
            <div className="detail-card-header">
              <h3>Roster ({roster.length})</h3>
              <div className="detail-card-actions">
                <button className="btn btn-sm btn-ghost" onClick={() => openCopyMove('copy')} title="Copy roster"><Copy size={14}/></button>
                <button className="btn btn-sm btn-ghost" onClick={() => openCopyMove('move')} title="Move roster"><ArrowRightLeft size={14}/></button>
                <button className="btn btn-sm btn-primary" onClick={openEnroll}><Plus size={14}/> Add</button>
              </div>
            </div>
            <div className="table-wrapper">
              <table>
                <thead><tr><th>Name</th><th>ID Number</th><th></th></tr></thead>
                <tbody>
                  {roster.map(s => (
                    <tr key={s.id}>
                      <td><Link to={`/app/students/${s.id}`}>{s.name}</Link></td>
                      <td>{s.id_number || '—'}</td>
                      <td><button className="btn btn-sm btn-ghost" onClick={() => unenroll(s.id)}><Trash2 size={14}/></button></td>
                    </tr>
                  ))}
                  {roster.length === 0 && <tr><td colSpan={3} className="empty-cell">No students enrolled</td></tr>}
                </tbody>
              </table>
            </div>
          </div>
        </div>

        {showEnroll && (
          <div className="modal-overlay" onClick={() => setShowEnroll(false)}>
            <div className="modal" onClick={e => e.stopPropagation()}>
              <h2>Enroll Student</h2>
              <div className="form-group">
                <label>Student</label>
                <select value={selectedStudent} onChange={e => setSelectedStudent(e.target.value)}>
                  <option value="">Choose...</option>
                  {available.map(s => <option key={s.id} value={s.id}>{s.full_name}</option>)}
                </select>
              </div>
              <div className="modal-actions">
                <button className="btn btn-ghost" onClick={() => setShowEnroll(false)}>Cancel</button>
                <button className="btn btn-primary" onClick={enroll} disabled={!selectedStudent}>Enroll</button>
              </div>
            </div>
          </div>
        )}

        {showCopy && (
          <div className="modal-overlay" onClick={() => setShowCopy(false)}>
            <div className="modal" onClick={e => e.stopPropagation()}>
              <h2>Copy Roster To</h2>
              <div className="form-group">
                <label>Target Class</label>
                <select value={targetCourse} onChange={e => setTargetCourse(e.target.value)}>
                  <option value="">Choose...</option>
                  {targetCourses.map(c => <option key={c.id} value={c.id}>{c.name}</option>)}
                </select>
              </div>
              <div className="modal-actions">
                <button className="btn btn-ghost" onClick={() => setShowCopy(false)}>Cancel</button>
                <button className="btn btn-primary" onClick={copyRoster} disabled={!targetCourse}>Copy</button>
              </div>
            </div>
          </div>
        )}

        {showMove && (
          <div className="modal-overlay" onClick={() => setShowMove(false)}>
            <div className="modal" onClick={e => e.stopPropagation()}>
              <h2>Move Roster To</h2>
              <div className="form-group">
                <label>Target Class</label>
                <select value={targetCourse} onChange={e => setTargetCourse(e.target.value)}>
                  <option value="">Choose...</option>
                  {targetCourses.map(c => <option key={c.id} value={c.id}>{c.name}</option>)}
                </select>
              </div>
              <div className="modal-actions">
                <button className="btn btn-ghost" onClick={() => setShowMove(false)}>Cancel</button>
                <button className="btn btn-primary" onClick={moveRoster} disabled={!targetCourse}>Move</button>
              </div>
            </div>
          </div>
        )}
      </div>
    </>
  );
}
