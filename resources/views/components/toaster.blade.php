@section('styles')
<style>
    @keyframes slideInRight {
        from { transform: translateX(100%); }
        to { transform: translateX(0); }
    }
    @keyframes slideOutRight {
        from { transform: translateX(0); }
        to { transform: translateX(100%); }
    }
    .toast-enter {
        animation: slideInRight 0.3s ease-out;
    }
    .toast-exit {
        animation: slideOutRight 0.3s ease-in;
    }
</style>
@endsection

<div id="toaster" class="fixed top-4 right-4 z-50 flex flex-col items-end pointer-events-none"></div>

<script>
    const toaster = document.getElementById('toaster');
    let toastId = 0;

    function addToast(type, message, duration = 5000) {
        const id = `toast-${toastId++}`;
        const toast = createToastElement(id, type, message);
        toaster.appendChild(toast);
        toast.classList.add('toast-enter');

        const progressBar = toast.querySelector('.progress-bar');
        const startTime = Date.now();
        const interval = setInterval(() => {
            const elapsedTime = Date.now() - startTime;
            const progress = 100 - (elapsedTime / duration) * 100;
            progressBar.style.width = `${Math.max(progress, 0)}%`;

            if (elapsedTime >= duration) {
                clearInterval(interval);
                removeToast(id);
            }
        }, 10);

        setTimeout(() => removeToast(id), duration);
    }

    function removeToast(id) {
        const toast = document.getElementById(id);
        if (toast) {
            toast.classList.add('toast-exit');
            setTimeout(() => toast.remove(), 300);
        }
    }

    function createToastElement(id, type, message) {
        const toast = document.createElement('div');
        toast.id = id;
        toast.className = `${getBackgroundColor(type)} p-3 rounded-lg shadow-lg w-full max-w-sm mb-3 pointer-events-auto`;
        toast.setAttribute('role', 'alert');

        toast.innerHTML = `
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    ${getIcon(type)}
                </div>
                <div class="ml-3 flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 whitespace-normal break-words">${message}</p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button class="bg-transparent rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" onclick="removeToast('${id}')">
                        <span class="sr-only">Close</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="mt-2 w-full bg-gray-200 rounded-full h-1">
                <div class="progress-bar h-1 rounded-full ${getProgressBarColor(type)}" style="width: 100%"></div>
            </div>
        `;

        return toast;
    }

    function getBackgroundColor(type) {
        switch (type) {
            case 'success': return 'bg-green-100';
            case 'error': return 'bg-red-100';
            case 'warning': return 'bg-yellow-100';
            case 'info': return 'bg-blue-100';
            default: return 'bg-gray-100';
        }
    }

    function getProgressBarColor(type) {
        switch (type) {
            case 'success': return 'bg-green-500';
            case 'error': return 'bg-red-500';
            case 'warning': return 'bg-yellow-500';
            case 'info': return 'bg-blue-500';
            default: return 'bg-gray-500';
        }
    }

    function getIcon(type) {
        switch (type) {
            case 'success':
                return '<svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>';
            case 'error':
                return '<svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>';
            case 'warning':
                return '<svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>';
            case 'info':
                return '<svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>';
            default:
                return '<svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>';
        }
    }
</script>
