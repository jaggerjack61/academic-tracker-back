import React, { useEffect, useState } from 'react';
import { useParams, Link } from 'react-router-dom';
import api from '../../api';
import { ArrowLeft } from 'lucide-react';

export default function TeacherDetail() {
  const { id } = useParams();
  const [teacher, setTeacher] = useState(null);

  useEffect(() => {
    api.get(`/teachers/${id}/`).then(r => {
      const data = r.data;
      const courses = (data.classes || []).map(c => ({
        id: c.course_id,
        name: c.course_name,
        grade_name: c.grade,
        subject_name: c.subject,
        student_count: c.student_count,
      }));
      setTeacher({ ...data.teacher, courses });
    });
  }, [id]);

  if (!teacher) return <div className="loading"><div className="spinner" /></div>;

  return (
    <>
      <div className="page-header">
        <Link to="/app/teachers" className="btn btn-ghost"><ArrowLeft size={16}/> Back</Link>
        <h1>{teacher.first_name} {teacher.last_name}</h1>
      </div>
      <div className="page-body">
        <div className="detail-grid">
          <div className="detail-card">
            <h3>Teacher Info</h3>
            <div className="detail-row"><span>Email</span><span>{teacher.user?.email}</span></div>
            <div className="detail-row"><span>Phone</span><span>{teacher.phone_number || '—'}</span></div>
            <div className="detail-row"><span>Address</span><span>{teacher.address || '—'}</span></div>
            <div className="detail-row"><span>Status</span><span className={`badge ${teacher.is_active ? 'badge-active' : 'badge-inactive'}`}>{teacher.is_active ? 'Active' : 'Inactive'}</span></div>
          </div>

          <div className="detail-card">
            <h3>Assigned Classes ({(teacher.courses || []).length})</h3>
            <div className="table-wrapper">
              <table>
                <thead><tr><th>Class</th><th>Grade</th><th>Subject</th><th>Students</th></tr></thead>
                <tbody>
                  {(teacher.courses || []).map(c => (
                    <tr key={c.id}>
                      <td><Link to={`/app/classes/${c.id}`}>{c.name}</Link></td>
                      <td>{c.grade_name}</td>
                      <td>{c.subject_name}</td>
                      <td>{c.student_count}</td>
                    </tr>
                  ))}
                  {(teacher.courses || []).length === 0 && (
                    <tr><td colSpan={4} className="empty-cell">No classes assigned</td></tr>
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
