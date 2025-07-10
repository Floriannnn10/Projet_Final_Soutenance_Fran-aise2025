import React from 'react';
import InputError from './InputError';
import InputLabel from './InputLabel';

const FormField = ({
    label,
    name,
    type = 'text',
    value,
    onChange,
    error,
    required = false,
    placeholder,
    options = [],
    rows = 3,
    className = '',
    ...props
}) => {
    const renderField = () => {
        switch (type) {
            case 'select':
                return (
                    <select
                        name={name}
                        value={value}
                        onChange={onChange}
                        className={`mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 ${error ? 'border-red-500' : ''} ${className}`}
                        {...props}
                    >
                        <option value="">Sélectionner...</option>
                        {options.map((option) => (
                            <option key={option.value} value={option.value}>
                                {option.label}
                            </option>
                        ))}
                    </select>
                );

            case 'textarea':
                return (
                    <textarea
                        name={name}
                        value={value}
                        onChange={onChange}
                        rows={rows}
                        placeholder={placeholder}
                        className={`mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 ${error ? 'border-red-500' : ''} ${className}`}
                        {...props}
                    />
                );

            case 'checkbox':
                return (
                    <input
                        type="checkbox"
                        name={name}
                        checked={value}
                        onChange={onChange}
                        className={`h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded ${error ? 'border-red-500' : ''} ${className}`}
                        {...props}
                    />
                );

            case 'date':
                return (
                    <input
                        type="date"
                        name={name}
                        value={value}
                        onChange={onChange}
                        placeholder={placeholder}
                        className={`mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 ${error ? 'border-red-500' : ''} ${className}`}
                        {...props}
                    />
                );

            case 'email':
                return (
                    <input
                        type="email"
                        name={name}
                        value={value}
                        onChange={onChange}
                        placeholder={placeholder}
                        className={`mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 ${error ? 'border-red-500' : ''} ${className}`}
                        {...props}
                    />
                );

            case 'password':
                return (
                    <input
                        type="password"
                        name={name}
                        value={value}
                        onChange={onChange}
                        placeholder={placeholder}
                        className={`mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 ${error ? 'border-red-500' : ''} ${className}`}
                        {...props}
                    />
                );

            default:
                return (
                    <input
                        type={type}
                        name={name}
                        value={value}
                        onChange={onChange}
                        placeholder={placeholder}
                        className={`mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 ${error ? 'border-red-500' : ''} ${className}`}
                        {...props}
                    />
                );
        }
    };

    return (
        <div className="mb-4">
            {label && (
                <InputLabel htmlFor={name} value={label} required={required} />
            )}
            
            {renderField()}
            
            {error && <InputError message={error} />}
        </div>
    );
};

export default FormField; 