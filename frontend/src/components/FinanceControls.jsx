import React, { useState } from 'react';
import { Search } from 'lucide-react';

export function FinanceControl({ label, hint, minWidth, grow = false, className = '', children }) {
  const style = minWidth ? { '--finance-control-width': minWidth } : undefined;
  const classes = ['finance-control'];

  if (grow) classes.push('finance-control--grow');
  if (className) classes.push(className);

  return (
    <label className={classes.join(' ')} style={style}>
      {label && <span className="finance-control__label">{label}</span>}
      {hint && <span className="finance-control__hint">{hint}</span>}
      <div className="finance-control__input">{children}</div>
    </label>
  );
}

function normalizeText(value) {
  return String(value || '').trim().toLowerCase();
}

export function SearchableSelectField({
  label,
  value,
  onChange,
  options,
  placeholder,
  searchPlaceholder,
  emptyMessage = 'No matching options',
  disabled = false,
}) {
  const [query, setQuery] = useState('');
  const normalizedQuery = normalizeText(query);
  const selectedOption = options.find(option => String(option.value) === String(value));
  const filteredOptions = normalizedQuery
    ? options.filter(option => normalizeText(`${option.label} ${option.searchText || ''} ${option.value}`).includes(normalizedQuery))
    : options;
  const renderedOptions = selectedOption && !filteredOptions.some(option => String(option.value) === String(selectedOption.value))
    ? [selectedOption, ...filteredOptions]
    : filteredOptions;

  const handleChange = (event) => {
    onChange(event.target.value);
    setQuery('');
  };

  return (
    <div className="form-group finance-searchable-select">
      <label>{label}</label>
      <div className="search-box finance-select-search">
        <Search size={16} />
        <input
          value={query}
          onChange={event => setQuery(event.target.value)}
          placeholder={searchPlaceholder}
          disabled={disabled}
        />
      </div>
      <select value={value} onChange={handleChange} disabled={disabled}>
        <option value="">{placeholder}</option>
        {renderedOptions.map(option => (
          <option key={option.value} value={option.value}>{option.label}</option>
        ))}
      </select>
      {normalizedQuery && renderedOptions.length === 0 && (
        <p className="finance-searchable-select__empty">{emptyMessage}</p>
      )}
    </div>
  );
}