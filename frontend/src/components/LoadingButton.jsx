import React from 'react';

export default function LoadingButton({
  loading = false,
  loadingText,
  disabled,
  className = 'btn btn-primary',
  children,
  ...props
}) {
  return (
    <button
      {...props}
      className={`${className}${loading ? ' is-loading' : ''}`}
      disabled={disabled || loading}
      aria-busy={loading || undefined}
    >
      {loading && <span className="btn-spinner" aria-hidden="true" />}
      <span className="btn-label">{loading && loadingText ? loadingText : children}</span>
    </button>
  );
}