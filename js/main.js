

document.addEventListener('DOMContentLoaded', function () {
    initializeApp();
});

function initializeApp() {

    aplicarMascaraCPF();

    aplicarMascaraTelefone();

    aplicarMascaraPlaca();

    autoHideAlerts();

    validarFormularios();

    initializeCollapsibleForms();
}


function initializeCollapsibleForms() {
    const btns = document.querySelectorAll('.toggle-btn');
    btns.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const header = this.closest('.card-header');
            const body = header.nextElementSibling;

            if (body && body.classList.contains('card-body')) {
                body.classList.toggle('collapsed');
                this.textContent = body.classList.contains('collapsed') ? '+' : '−';
            }
        });
    });
}


function aplicarMascaraCPF() {
    const inputs = document.querySelectorAll('input[name="cpf"]');
    inputs.forEach(input => {
        input.addEventListener('input', function () {
            let value = this.value.replace(/\D/g, '');
            value = value.slice(0, 11);
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            this.value = value;
        });
    });
}


function aplicarMascaraTelefone() {
    const inputs = document.querySelectorAll('input[name="telefone"]');
    inputs.forEach(input => {
        input.addEventListener('input', function () {
            let value = this.value.replace(/\D/g, '');
            value = value.slice(0, 11);
            if (value.length <= 2) {
                this.value = value;
                return;
            }

            if (value.length <= 7) {
                this.value = value.replace(/^(\d{2})(\d+)/, '($1) $2');
                return;
            }

            value = value.replace(/^(\d{2})(\d{5})(\d+)/, '($1) $2-$3');
            this.value = value;
        });
    });
}


function aplicarMascaraPlaca() {
    const inputs = document.querySelectorAll('input[name="placa"]');
    inputs.forEach(input => {
        input.addEventListener('input', function () {
            this.value = this.value.toUpperCase().substring(0, 10);
        });
    });
}


function autoHideAlerts() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.3s ease';
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 300);
        }, 5000);
    });
}


function validarFormularios() {
    const forms = document.querySelectorAll('.form');
    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            if (!validarFormulario(this)) {
                e.preventDefault();
                return;
            }

            const cpfInput = this.querySelector('input[name="cpf"]');
            if (cpfInput) {
                cpfInput.value = cpfInput.value.replace(/\D/g, '').slice(0, 11);
            }

            const telefoneInput = this.querySelector('input[name="telefone"]');
            if (telefoneInput) {
                telefoneInput.value = telefoneInput.value.replace(/\D/g, '').slice(0, 11);
            }

            const dados = Object.fromEntries(new FormData(this).entries());
            console.log('Dados enviados no submit:', dados);
        });
    });
}

function validarFormulario(form) {
    let valid = true;
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');

    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.style.borderColor = '#dc3545';
            valid = false;
        } else {
            input.style.borderColor = '';
        }
    });

    return valid;
}


function formatarMoeda(valor) {
    return 'R$ ' + parseFloat(valor).toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}


function confirmarExclusao(mensagem = 'Tem certeza que deseja deletar este registro?') {
    return confirm(mensagem);
}


function toggle(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.style.display = element.style.display === 'none' ? 'block' : 'none';
    }
}


function addClass(elementId, className) {
    const element = document.getElementById(elementId);
    if (element) {
        element.classList.add(className);
    }
}


function removeClass(elementId, className) {
    const element = document.getElementById(elementId);
    if (element) {
        element.classList.remove(className);
    }
}


function exibirNotificacao(mensagem, tipo = 'info') {
    const container = document.createElement('div');
    container.className = `alert alert-${tipo}`;
    container.textContent = mensagem;

    const mainArea = document.querySelector('.main-area');
    mainArea.insertBefore(container, mainArea.firstChild);

    setTimeout(() => {
        container.remove();
    }, 5000);
}


function ordenarTabela(elementId, coluna) {
    const tabela = document.getElementById(elementId);
    if (!tabela) return;

    const tbody = tabela.querySelector('tbody');
    const linhas = Array.from(tbody.querySelectorAll('tr'));

    let ascending = true;
    if (tabela.dataset.lastColumn === coluna) {
        ascending = !tabela.dataset.ascending === 'true';
    }

    linhas.sort((a, b) => {
        const aValue = a.children[coluna].textContent.trim();
        const bValue = b.children[coluna].textContent.trim();

        if (!isNaN(aValue) && !isNaN(bValue)) {
            return ascending ? aValue - bValue : bValue - aValue;
        } else {
            return ascending ?
                aValue.localeCompare(bValue) :
                bValue.localeCompare(aValue);
        }
    });

    linhas.forEach(linha => tbody.appendChild(linha));

    tabela.dataset.lastColumn = coluna;
    tabela.dataset.ascending = ascending;
}


function buscarEmTabela(inputId, tabelaId) {
    const input = document.getElementById(inputId);
    const tabela = document.getElementById(tabelaId);

    if (!input || !tabela) return;

    input.addEventListener('keyup', function () {
        const termo = this.value.toLowerCase();
        const linhas = tabela.querySelectorAll('tbody tr');

        linhas.forEach(linha => {
            const texto = linha.textContent.toLowerCase();
            linha.style.display = texto.includes(termo) ? '' : 'none';
        });
    });
}


function exportarParaCSV(tabelaId, nomeArquivo = 'relatorio.csv') {
    const tabela = document.getElementById(tabelaId);
    if (!tabela) return;

    let csv = [];

    const headers = Array.from(tabela.querySelectorAll('th')).map(th => th.textContent);
    csv.push(headers.join(','));

    const linhas = tabela.querySelectorAll('tbody tr');
    linhas.forEach(linha => {
        const cels = Array.from(linha.querySelectorAll('td')).map(td => {
            let texto = td.textContent.trim();

            texto = texto.replace(/"/g, '""');

            if (texto.includes(',')) {
                texto = `"${texto}"`;
            }
            return texto;
        });
        csv.push(cels.join(','));
    });

    const blob = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);

    link.setAttribute('href', url);
    link.setAttribute('download', nomeArquivo);
    link.style.visibility = 'hidden';

    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}


function imprimirTabela(tabelaId) {
    const tabela = document.getElementById(tabelaId);
    if (!tabela) return;

    const janelaImpressao = window.open('', '', 'height=600,width=800');
    janelaImpressao.document.write('<html><head><title>Impressão</title>');
    janelaImpressao.document.write('<link rel="stylesheet" href="css/style.css">');
    janelaImpressao.document.write('</head><body>');
    janelaImpressao.document.write(tabela.outerHTML);
    janelaImpressao.document.write('</body></html>');
    janelaImpressao.document.close();

    setTimeout(() => {
        janelaImpressao.print();
    }, 250);
}


function calcularDataDiasDepois(dias) {
    const data = new Date();
    data.setDate(data.getDate() + dias);
    return data.toISOString().split('T')[0];
}


function formatarData(data) {
    const d = new Date(data);
    const dia = String(d.getDate()).padStart(2, '0');
    const mes = String(d.getMonth() + 1).padStart(2, '0');
    const ano = d.getFullYear();
    return `${dia}/${mes}/${ano}`;
}


function debug(mensagem, objeto = null) {
    if (typeof console !== 'undefined' && console.log) {
        console.log('[DEBUG]', mensagem, objeto);
    }
}
