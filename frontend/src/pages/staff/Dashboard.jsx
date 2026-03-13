import React, { useEffect, useRef, useState } from 'react';
import api from '../../api';
import { Users, GraduationCap, TrendingDown, TrendingUp } from 'lucide-react';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, LineChart, Line } from 'recharts';

const emptyChartSize = { width: 0, height: 0 };

export default function Dashboard() {
  const [data, setData] = useState(null);
  const [classChartSize, setClassChartSize] = useState(emptyChartSize);
  const [absenceChartSize, setAbsenceChartSize] = useState(emptyChartSize);
  const classChartRef = useRef(null);
  const absenceChartRef = useRef(null);

  useEffect(() => {
    api.get('/dashboard/staff/').then(r => setData(r.data));
  }, []);

  useEffect(() => {
    if (!data) return undefined;

    const observeChart = (element, setSize) => {
      if (!element) return () => {};

      const updateSize = (width, height) => {
        const nextWidth = Math.max(Math.floor(width), 0);
        const nextHeight = Math.max(Math.floor(height), 0);

        if (nextWidth > 0 && nextHeight > 0) {
          setSize(current => (
            current.width === nextWidth && current.height === nextHeight
              ? current
              : { width: nextWidth, height: nextHeight }
          ));
        }
      };

      const initialBounds = element.getBoundingClientRect();
      updateSize(initialBounds.width, initialBounds.height);

      if (typeof ResizeObserver === 'undefined') {
        return () => {};
      }

      const observer = new ResizeObserver(entries => {
        const entry = entries[0];
        if (!entry) return;
        updateSize(entry.contentRect.width, entry.contentRect.height);
      });

      observer.observe(element);

      return () => observer.disconnect();
    };

    const cleanupClassChart = observeChart(classChartRef.current, setClassChartSize);
    const cleanupAbsenceChart = observeChart(absenceChartRef.current, setAbsenceChartSize);

    return () => {
      cleanupClassChart();
      cleanupAbsenceChart();
    };
  }, [data]);

  if (!data) return <div className="loading"><div className="spinner" /></div>;

  const absenceDiff = data.current_absences - data.prior_absences;

  return (
    <>
      <div className="page-header">
        <h1>Dashboard</h1>
        <p>Overview of academic operations</p>
      </div>
      <div className="page-body">
        <div className="stats-grid">
          <div className="stat-card">
            <div className="stat-icon purple"><GraduationCap size={24} /></div>
            <div>
              <div className="stat-value">{data.student_count}</div>
              <div className="stat-label">Active Students</div>
            </div>
          </div>
          <div className="stat-card">
            <div className="stat-icon green"><Users size={24} /></div>
            <div>
              <div className="stat-value">{data.teacher_count}</div>
              <div className="stat-label">Active Teachers</div>
            </div>
          </div>
          <div className="stat-card">
            <div className="stat-icon orange">
              {absenceDiff > 0 ? <TrendingUp size={24} /> : <TrendingDown size={24} />}
            </div>
            <div>
              <div className="stat-value">{data.current_absences}</div>
              <div className="stat-label">
                Absences (5d) {absenceDiff !== 0 && (
                  <span style={{ color: absenceDiff > 0 ? 'var(--danger)' : 'var(--success)', fontSize: '0.75rem' }}>
                    {absenceDiff > 0 ? '+' : ''}{absenceDiff} vs prior
                  </span>
                )}
              </div>
            </div>
          </div>
        </div>

        <div className="grid-2">
          <div className="card">
            <div className="card-header"><h3>Class Distribution</h3></div>
            <div className="card-body">
              {data.class_distribution.length > 0 ? (
                <div className="chart-container" ref={classChartRef}>
                  {classChartSize.width > 0 && classChartSize.height > 0 && (
                    <BarChart width={classChartSize.width} height={classChartSize.height} data={data.class_distribution}>
                      <CartesianGrid strokeDasharray="3 3" stroke="var(--border)" />
                      <XAxis dataKey="name" tick={{ fill: 'var(--text-secondary)', fontSize: 12 }} />
                      <YAxis tick={{ fill: 'var(--text-secondary)', fontSize: 12 }} />
                      <Tooltip
                        contentStyle={{ background: 'var(--bg-card)', border: '1px solid var(--border)', borderRadius: 8 }}
                        labelStyle={{ color: 'var(--text-primary)' }}
                      />
                      <Bar dataKey="student_count" fill="var(--accent)" radius={[4, 4, 0, 0]} name="Students" />
                    </BarChart>
                  )}
                </div>
              ) : (
                <div className="empty-state"><p>No classes yet</p></div>
              )}
            </div>
          </div>

          <div className="card">
            <div className="card-header"><h3>Absence Trend (5 Days)</h3></div>
            <div className="card-body">
              <div className="chart-container" ref={absenceChartRef}>
                {absenceChartSize.width > 0 && absenceChartSize.height > 0 && (
                  <LineChart width={absenceChartSize.width} height={absenceChartSize.height} data={data.absence_trend}>
                    <CartesianGrid strokeDasharray="3 3" stroke="var(--border)" />
                    <XAxis dataKey="date" tick={{ fill: 'var(--text-secondary)', fontSize: 12 }} />
                    <YAxis tick={{ fill: 'var(--text-secondary)', fontSize: 12 }} allowDecimals={false} />
                    <Tooltip
                      contentStyle={{ background: 'var(--bg-card)', border: '1px solid var(--border)', borderRadius: 8 }}
                      labelStyle={{ color: 'var(--text-primary)' }}
                    />
                    <Line type="monotone" dataKey="count" stroke="var(--danger)" strokeWidth={2} dot={{ fill: 'var(--danger)', r: 4 }} name="Absences" />
                  </LineChart>
                )}
              </div>
            </div>
          </div>
        </div>

        <div className="card" style={{ marginTop: '1.5rem' }}>
          <div className="card-header"><h3>Recent Students</h3></div>
          <div className="table-wrapper">
            <table>
              <thead>
                <tr>
                  <th>Name</th>
                  <th>ID Number</th>
                  <th>Phone</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                {data.recent_students.map(s => (
                  <tr key={s.id}>
                    <td>{s.first_name} {s.last_name}</td>
                    <td>{s.id_number}</td>
                    <td>{s.phone_number || '—'}</td>
                    <td><span className={`badge ${s.is_active ? 'badge-active' : 'badge-inactive'}`}>{s.is_active ? 'Active' : 'Inactive'}</span></td>
                  </tr>
                ))}
                {data.recent_students.length === 0 && (
                  <tr><td colSpan={4} style={{ textAlign: 'center', color: 'var(--text-muted)' }}>No students yet</td></tr>
                )}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </>
  );
}
