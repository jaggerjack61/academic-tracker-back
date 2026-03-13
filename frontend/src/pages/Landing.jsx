import React from 'react';
import { Link } from 'react-router-dom';
import {
  ArrowRight, CheckCircle2, Compass, LibraryBig,
  MessagesSquare, School, Wallet,
} from 'lucide-react';

export default function Landing() {
  return (
    <div className="land">
      {/* ── Hero ── */}
      <section className="land-hero">
        <div className="land-hero-noise" aria-hidden="true" />
        <div className="land-hero-glow land-hero-glow--a" aria-hidden="true" />
        <div className="land-hero-glow land-hero-glow--b" aria-hidden="true" />

        <nav className="land-nav">
          <div className="land-logo">
            <School size={18} />
            <span>Academic Tracker</span>
          </div>
          <Link to="/login" className="land-nav-link">Sign in <ArrowRight size={14} /></Link>
        </nav>

        <div className="land-hero-body">
          <h1>Every school operation,<br /><em>one product.</em></h1>
          <p>Attendance, classes, student records, fee management, and messaging — connected for staff, students, and families.</p>
          <div className="land-hero-actions">
            <Link to="/login" className="land-btn land-btn--gold">
              Get started <ArrowRight size={16} />
            </Link>
            <Link to="/password-reset" className="land-btn land-btn--outline">
              Reset access
            </Link>
          </div>
        </div>

        <div className="land-hero-chips">
          <span className="land-chip land-chip--onboarding"><Compass size={14} /> Onboarding</span>
          <span className="land-chip land-chip--elearning"><LibraryBig size={14} /> Elearning</span>
          <span className="land-chip land-chip--finance"><Wallet size={14} /> Finance</span>
          <span className="land-chip land-chip--collab"><MessagesSquare size={14} /> Collab</span>
        </div>
      </section>

      {/* ── Modules ── */}
      <section className="land-modules">
        <div className="land-modules-inner">
          <p className="land-kicker">The platform</p>
          <h2>Four modules, one workflow</h2>
          <p className="land-modules-sub">Each module covers a distinct part of daily school operations. Staff and students pick the modules they need.</p>

          <div className="land-grid">
            <article className="land-card land-card--onboarding">
              <div className="land-card-head">
                <div className="land-card-icon"><Compass size={20} /></div>
                <span className="land-card-label">Onboarding</span>
              </div>
              <h3>Records, access &amp; readiness</h3>
              <p>Set up people, manage student intake, family records, and staff access before the school day starts moving.</p>
              <ul className="land-card-list">
                <li><CheckCircle2 size={14} /> Student and family records</li>
                <li><CheckCircle2 size={14} /> Staff access and settings</li>
                <li><CheckCircle2 size={14} /> Roster always prepared</li>
              </ul>
            </article>

            <article className="land-card land-card--elearning">
              <div className="land-card-head">
                <div className="land-card-icon"><LibraryBig size={20} /></div>
                <span className="land-card-label">Elearning</span>
              </div>
              <h3>Teaching, coursework &amp; delivery</h3>
              <p>Coordinate classes, track attendance, capture daily academic activity, and keep classroom delivery structured.</p>
              <ul className="land-card-list">
                <li><CheckCircle2 size={14} /> Daily attendance capture</li>
                <li><CheckCircle2 size={14} /> Roster and activity management</li>
                <li><CheckCircle2 size={14} /> Assignment tracking</li>
              </ul>
            </article>

            <article className="land-card land-card--finance">
              <div className="land-card-head">
                <div className="land-card-icon"><Wallet size={20} /></div>
                <span className="land-card-label">Finance</span>
              </div>
              <h3>Fees, payments &amp; accounts</h3>
              <p>Track every fee owed, record every payment, flag outstanding balances, and keep finances transparent.</p>
              <ul className="land-card-list">
                <li><CheckCircle2 size={14} /> Full payment audit trail</li>
                <li><CheckCircle2 size={14} /> Arrears flagging per term</li>
                <li><CheckCircle2 size={14} /> Fee structures and plans</li>
              </ul>
            </article>

            <article className="land-card land-card--collab">
              <div className="land-card-head">
                <div className="land-card-icon"><MessagesSquare size={20} /></div>
                <span className="land-card-label">Collab</span>
              </div>
              <h3>Messages, groups &amp; chats</h3>
              <p>Direct messages between staff and students, group conversations, and automatic class group chats in one place.</p>
              <ul className="land-card-list">
                <li><CheckCircle2 size={14} /> Direct and group messaging</li>
                <li><CheckCircle2 size={14} /> Auto class group per course</li>
                <li><CheckCircle2 size={14} /> Custom department groups</li>
              </ul>
            </article>
          </div>
        </div>
      </section>

      {/* ── Bottom CTA ── */}
      <section className="land-cta-section">
        <div className="land-cta-inner">
          <h2>Built for the complete school day.</h2>
          <p>From first bell to final follow-up.</p>
          <Link to="/login" className="land-btn land-btn--gold">
            Sign in now <ArrowRight size={16} />
          </Link>
        </div>
      </section>
    </div>
  );
}
