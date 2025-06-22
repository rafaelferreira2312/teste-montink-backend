// Mini ERP - JavaScript Customizado

// Inicialização
document.addEventListener('DOMContentLoaded', function() {
    initializeCustomFeatures();
});

// Funcionalidades customizadas
function initializeCustomFeatures() {
    // Smooth scroll para links âncora
    initSmoothScroll();
    
    // Auto-resize para textareas
    initAutoResize();
    
    // Confirmação para ações destrutivas
    initConfirmActions();
    
    // Preview de imagens
    initImagePreview();
    
    // Contador de caracteres
    initCharCounter();
}

// Smooth scroll
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Auto-resize para textareas
function initAutoResize() {
    document.querySelectorAll('textarea[data-auto-resize]').forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
    });
}

// Confirmação para ações destrutivas
function initConfirmActions() {
    document.querySelectorAll('[data-confirm]').forEach(element => {
        element.addEventListener('click', function(e) {
            const message = this.getAttribute('data-confirm');
            if (!confirm(message)) {
                e.preventDefault();
                return false;
            }
        });
    });
}

// Preview de imagens
function initImagePreview() {
    document.querySelectorAll('input[type="file"][data-preview]').forEach(input => {
        input.addEventListener('change', function() {
            const file = this.files[0];
            const previewId = this.getAttribute('data-preview');
            const preview = document.getElementById(previewId);
            
            if (file && preview) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    });
}

// Contador de caracteres
function initCharCounter() {
    document.querySelectorAll('[data-char-counter]').forEach(element => {
        const maxLength = element.getAttribute('maxlength') || element.getAttribute('data-max-length');
        if (maxLength) {
            // Criar contador
            const counter = document.createElement('small');
            counter.className = 'text-muted char-counter';
            element.parentNode.appendChild(counter);
            
            // Atualizar contador
            const updateCounter = () => {
                const currentLength = element.value.length;
                counter.textContent = `${currentLength}/${maxLength}`;
                
                if (currentLength > maxLength * 0.9) {
                    counter.className = 'text-warning char-counter';
                } else if (currentLength === maxLength) {
                    counter.className = 'text-danger char-counter';
                } else {
                    counter.className = 'text-muted char-counter';
                }
            };
            
            // Event listeners
            element.addEventListener('input', updateCounter);
            element.addEventListener('keyup', updateCounter);
            
            // Inicializar
            updateCounter();
        }
    });
}

// Utilitários globais
window.MiniERPUtils = {
    // Debounce function
    debounce: function(func, wait, immediate) {
        let timeout;
        return function executedFunction() {
            const context = this;
            const args = arguments;
            const later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            const callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    },
    
    // Throttle function
    throttle: function(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    },
    
    // Validação de CPF/CNPJ
    validateCPF: function(cpf) {
        cpf = cpf.replace(/[^\d]+/g, '');
        if (cpf.length !== 11) return false;
        
        // Verificar se todos os dígitos são iguais
        if (/^(\d)\1{10}$/.test(cpf)) return false;
        
        // Validar dígitos verificadores
        let soma = 0;
        for (let i = 0; i < 9; i++) {
            soma += parseInt(cpf.charAt(i)) * (10 - i);
        }
        let resto = (soma * 10) % 11;
        if (resto === 10 || resto === 11) resto = 0;
        if (resto !== parseInt(cpf.charAt(9))) return false;
        
        soma = 0;
        for (let i = 0; i < 10; i++) {
            soma += parseInt(cpf.charAt(i)) * (11 - i);
        }
        resto = (soma * 10) % 11;
        if (resto === 10 || resto === 11) resto = 0;
        if (resto !== parseInt(cpf.charAt(10))) return false;
        
        return true;
    },
    
    // Copiar para clipboard
    copyToClipboard: function(text) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(() => {
                showToast('Copiado para a área de transferência!', 'success');
            });
        } else {
            // Fallback para navegadores mais antigos
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
                showToast('Copiado para a área de transferência!', 'success');
            } catch (err) {
                showToast('Erro ao copiar texto', 'error');
            }
            document.body.removeChild(textArea);
        }
    }
};
