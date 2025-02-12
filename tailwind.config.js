module.exports = {
    // ... outras configurações
    safelist: [
        'text-red-500',
        'text-primary-500',
        'text-success-500',
        'text-gray-500',
        // Cores para bordas
        'hover:border-orange-600',
        'hover:border-blue-600',
        'hover:border-green-600',
        'hover:border-gray-500',
        'border-orange-600',
        'border-blue-600',
        'border-green-600',
        'border-gray-200',
        // Cores para backgrounds
        'hover:bg-orange-50',
        'hover:bg-blue-50',
        'hover:bg-green-50',
        'hover:bg-gray-50',
        'bg-orange-50/30',
        'bg-blue-50/30',
        'bg-green-50/30',
        'bg-gray-50/30',
        'bg-opacity-10',
    ],
    // ... resto das configurações
    theme: {
        extend: {
            opacity: {
                '10': '0.1',
                '90': '0.9',
            }
        }
    }
} 