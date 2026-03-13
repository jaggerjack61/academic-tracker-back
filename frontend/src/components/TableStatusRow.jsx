import React from 'react';

export default function TableStatusRow({
  loading,
  hasRows,
  colSpan,
  loadingMessage = 'Loading data...',
  emptyMessage = 'No records found',
}) {
  if (loading) {
    return (
      <tr>
        <td colSpan={colSpan} className="table-state-cell">
          <div className="table-inline-loading">
            <span className="spinner spinner-sm" aria-hidden="true" />
            <span>{loadingMessage}</span>
          </div>
        </td>
      </tr>
    );
  }

  if (hasRows) {
    return null;
  }

  return (
    <tr>
      <td colSpan={colSpan} className="empty-cell">{emptyMessage}</td>
    </tr>
  );
}