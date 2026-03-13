import React, { useEffect, useState, useCallback, useRef } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import api from '../../api';
import { useAuth } from '../../AuthContext';
import { useToast } from '../../ToastContext';
import { ArrowLeft, Send, Users, UserPlus, UserMinus } from 'lucide-react';

export default function CollabChat() {
  const { id } = useParams();
  const { role, profile: myProfile } = useAuth();
  const { error: toastError, success: toastSuccess } = useToast();
  const navigate = useNavigate();
  const basePath = role === 'student' ? '/student/collab' : '/app/collab';

  const [group, setGroup] = useState(null);
  const [members, setMembers] = useState([]);
  const [messages, setMessages] = useState([]);
  const [msgTotal, setMsgTotal] = useState(0);
  const [msgPage, setMsgPage] = useState(1);
  const [content, setContent] = useState('');
  const [sending, setSending] = useState(false);
  const [loading, setLoading] = useState(true);
  const [showMembers, setShowMembers] = useState(false);
  const messagesEndRef = useRef(null);
  const latestMessageIdRef = useRef(0);

  const load = useCallback((pg = 1) => {
    setLoading(true);
    api.get(`/collab/groups/${id}/`, { params: { page: pg, page_size: 50 } })
      .then(r => {
        setGroup(r.data.group);
        setMembers(r.data.members);
        setMessages(r.data.messages.results);
        setMsgTotal(r.data.messages.total);
        setMsgPage(pg);
      })
      .catch(() => toastError('Failed to load conversation'))
      .finally(() => setLoading(false));
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [id]);

  useEffect(() => {
    load();
  }, [load]);

  useEffect(() => {
    latestMessageIdRef.current = messages.at(-1)?.id ?? 0;
  }, [messages]);

  const syncMessages = useCallback(() => {
    if (!latestMessageIdRef.current || document.visibilityState !== 'visible') {
      return;
    }

    api.get(`/collab/groups/${id}/messages/`, { params: { after_id: latestMessageIdRef.current } })
      .then(r => {
        if (r.data.messages.length > 0) {
          setMessages(prev => {
            const seen = new Set(prev.map(msg => msg.id));
            const next = [...prev];

            for (const msg of r.data.messages) {
              if (!seen.has(msg.id)) {
                next.push(msg);
              }
            }

            return next;
          });
        }

        setMsgTotal(r.data.total);
      })
      .catch(() => {});
  }, [id]);

  useEffect(() => {
    const handleFocus = () => syncMessages();
    const handleVisibilityChange = () => {
      if (document.visibilityState === 'visible') {
        syncMessages();
      }
    };

    window.addEventListener('focus', handleFocus);
    document.addEventListener('visibilitychange', handleVisibilityChange);

    return () => {
      window.removeEventListener('focus', handleFocus);
      document.removeEventListener('visibilitychange', handleVisibilityChange);
    };
  }, [syncMessages]);

  useEffect(() => {
    messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  }, [messages]);

  const handleSend = async (e) => {
    e.preventDefault();
    if (!content.trim() || sending) return;
    setSending(true);
    try {
      const r = await api.post(`/collab/groups/${id}/send/`, { content: content.trim() });
      setMessages(prev => [...prev, r.data]);
      setMsgTotal(prev => prev + 1);
      setContent('');
    } catch {
      toastError('Failed to send message');
    } finally {
      setSending(false);
    }
  };

  const handleRemoveMember = async (profileId) => {
    try {
      await api.post(`/collab/groups/${id}/remove-member/`, { profile_id: profileId });
      toastSuccess('Member removed');
      load();
    } catch {
      toastError('Failed to remove member');
    }
  };

  const loadMore = () => {
    const nextPage = msgPage + 1;
    api.get(`/collab/groups/${id}/`, { params: { page: nextPage, page_size: 50 } })
      .then(r => {
        setMessages(prev => [...r.data.messages.results, ...prev]);
        setMsgPage(nextPage);
      })
      .catch(() => toastError('Failed to load older messages'));
  };

  const formatTime = (iso) => {
    const d = new Date(iso);
    return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  };

  const formatDate = (iso) => {
    const d = new Date(iso);
    const today = new Date();
    if (d.toDateString() === today.toDateString()) return 'Today';
    const yesterday = new Date(today);
    yesterday.setDate(yesterday.getDate() - 1);
    if (d.toDateString() === yesterday.toDateString()) return 'Yesterday';
    return d.toLocaleDateString(undefined, { weekday: 'short', month: 'short', day: 'numeric' });
  };

  // Group messages by date
  const groupedMessages = [];
  let lastDate = '';
  for (const msg of messages) {
    const date = new Date(msg.created_at).toDateString();
    if (date !== lastDate) {
      groupedMessages.push({ type: 'date', date: msg.created_at });
      lastDate = date;
    }
    groupedMessages.push({ type: 'msg', ...msg });
  }

  if (loading && !group) return <div className="loading"><div className="spinner" /></div>;

  const canManageMembers = group && !group.is_class_group && (
    role === 'admin' || (myProfile && group.created_by === myProfile.id)
  );

  return (
    <div className="collab-chat-shell">
      <div className="collab-chat-header">
        <button className="btn btn-icon" onClick={() => navigate(basePath)} title="Back">
          <ArrowLeft size={18} />
        </button>
        <div className="collab-chat-header-info">
          <strong>{group?.name}</strong>
          <span>{members.length} member{members.length !== 1 ? 's' : ''}</span>
        </div>
        <button
          className={`btn btn-icon ${showMembers ? 'active' : ''}`}
          onClick={() => setShowMembers(v => !v)}
          title="Members"
        >
          <Users size={18} />
        </button>
      </div>

      <div className="collab-chat-body">
        <div className="collab-chat-messages">
          {msgTotal > messages.length && (
            <div className="collab-load-more">
              <button className="btn btn-secondary btn-sm" onClick={loadMore}>Load older messages</button>
            </div>
          )}
          {groupedMessages.map((item, i) =>
            item.type === 'date' ? (
              <div key={`date-${i}`} className="collab-date-divider">
                <span>{formatDate(item.date)}</span>
              </div>
            ) : (
              <div
                key={item.id}
                className={`collab-msg ${item.sender_id === myProfile?.id ? 'collab-msg-mine' : ''}`}
              >
                {item.sender_id !== myProfile?.id && (
                  <div className="collab-msg-sender">
                    {item.sender_name}
                    <span className="collab-msg-role">{item.sender_type}</span>
                  </div>
                )}
                <div className="collab-msg-bubble">
                  <p>{item.content}</p>
                  <span className="collab-msg-time">{formatTime(item.created_at)}</span>
                </div>
              </div>
            )
          )}
          <div ref={messagesEndRef} />
        </div>

        {showMembers && (
          <div className="collab-members-panel">
            <h3>Members</h3>
            <div className="collab-members-list">
              {members.map(m => (
                <div key={m.id} className="collab-member-row">
                  <div>
                    <strong>{m.profile.full_name}</strong>
                    <span className="badge badge-accent">{m.profile.type}</span>
                  </div>
                  {canManageMembers && m.profile.id !== myProfile?.id && (
                    <button
                      className="btn btn-icon btn-sm"
                      title="Remove member"
                      onClick={() => handleRemoveMember(m.profile.id)}
                    >
                      <UserMinus size={14} />
                    </button>
                  )}
                </div>
              ))}
            </div>
            {canManageMembers && (
              <button
                className="btn btn-secondary btn-sm"
                style={{ marginTop: '0.5rem', width: '100%' }}
                onClick={() => navigate(`${basePath}/${id}/add-members`)}
              >
                <UserPlus size={14} /> Add Members
              </button>
            )}
          </div>
        )}
      </div>

      <form className="collab-chat-input" onSubmit={handleSend}>
        <input
          type="text"
          placeholder="Type a message…"
          value={content}
          onChange={e => setContent(e.target.value)}
          autoFocus
        />
        <button className="btn btn-primary" type="submit" disabled={!content.trim() || sending}>
          <Send size={16} />
        </button>
      </form>
    </div>
  );
}
