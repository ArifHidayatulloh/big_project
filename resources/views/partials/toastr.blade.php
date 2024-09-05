<!-- Toast Container -->
<div id="toast-container"></div>

<!-- Toast Styles -->
<style>
    #toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1050; /* Make sure it's above other content */
    }

    .toast {
        background: #333;
        color: #fff;
        padding: 15px;
        border-radius: 5px;
        margin: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        opacity: 0;
        transition: opacity 0.3s, transform 0.3s;
    }

    .toast.show {
        opacity: 1;
        transform: translateY(0);
    }

    .toast.hide {
        opacity: 0;
        transform: translateY(20px);
    }

    .toast-success {
        background: #28a745;
    }

    .toast-error {
        background: #dc3545;
    }
</style>

<!-- Toast JavaScript -->
<script>
    function showToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `toast ${type === 'success' ? 'toast-success' : 'toast-error'}`;
        toast.innerText = message;

        const container = document.getElementById('toast-container');
        container.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('show');
        }, 100);

        setTimeout(() => {
            toast.classList.remove('show');
            toast.classList.add('hide');
            setTimeout(() => {
                container.removeChild(toast);
            }, 300);
        }, 5000); // Display toast for 5 seconds
    }

    document.addEventListener('DOMContentLoaded', function() {
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                showToast('{{ $error }}', 'error');
            @endforeach
        @endif

        @if (session('success'))
            showToast('{{ session('success') }}', 'success');
        @endif
    });
</script>
