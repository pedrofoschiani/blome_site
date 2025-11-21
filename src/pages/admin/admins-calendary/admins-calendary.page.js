// --- Script do Carrossel (Mantenha igual) ---
const wrapper = document.getElementById('calendarWrapper');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');

if (wrapper) {
    const scrollAmount = 300;
    const updateArrows = () => {
        const maxScroll = wrapper.scrollWidth - wrapper.clientWidth;
        prevBtn.classList.toggle('hidden', wrapper.scrollLeft <= 0);
        nextBtn.classList.toggle('hidden', wrapper.scrollLeft >= maxScroll - 5);
    };
    prevBtn.addEventListener('click', () => { wrapper.scrollBy({ left: -scrollAmount, behavior: 'smooth' }); setTimeout(updateArrows, 500); });
    nextBtn.addEventListener('click', () => { wrapper.scrollBy({ left: scrollAmount, behavior: 'smooth' }); setTimeout(updateArrows, 500); });
    wrapper.addEventListener('scroll', updateArrows);
    updateArrows();
    window.addEventListener('resize', updateArrows);
}

// --- LÓGICA DO MODAL ---
const modal = document.getElementById('scheduleModal');
const formAction = document.getElementById('formAction');
const scheduleId = document.getElementById('scheduleId');
const dayOfWeekInput = document.getElementById('dayOfWeekInput');
const startTime = document.getElementById('startTime');
const endTime = document.getElementById('endTime');
const subjectSelect = document.getElementById('subjectSelect');
const profSelect = document.getElementById('profSelect');
const modalTitle = document.getElementById('modalTitle');
const btnDelete = document.getElementById('btnDelete');
const deleteId = document.getElementById('deleteId');
const deleteForm = document.getElementById('deleteForm');

// Função auxiliar de formatação
function formatTime(timeString) {
    if (!timeString) return "";
    return timeString.substring(0, 5);
}

function showModal() {
    if (modal) {
        modal.classList.add('open');
        modal.style.display = 'flex';
    }
}

function closeModal() {
    modal.classList.remove('open');
    setTimeout(() => { modal.style.display = 'none'; }, 300);
}

// --- CORREÇÃO PRINCIPAL AQUI: Filtro de Matérias ---
function updateSubjects(preSelectedSubjectId = null) {
    const profId = profSelect.value;
    
    // Limpa opções anteriores
    subjectSelect.innerHTML = '<option value="">Selecione...</option>';
    
    if (!profId) {
        subjectSelect.disabled = true;
        subjectSelect.innerHTML = '<option value="">Primeiro selecione o professor</option>';
        return;
    }

    // Recupera a lista de matérias do professor (Do PHP)
    // Converte tudo para String para garantir comparação segura
    let allowedSubjectIds = (profSubjectMap[profId] || []).map(String);

    // Depuração: descomente se precisar ver o que o PHP enviou
    console.log("Professor ID:", profId, "Matérias permitidas:", allowedSubjectIds);

    // FALLBACK DE SEGURANÇA PARA EDIÇÃO:
    // Se estamos editando e existe uma matéria já salva (preSelectedSubjectId),
    // mas ela não está na lista "allowed" (talvez o vínculo foi removido),
    // nós a forçamos na lista para que o formulário não quebre.
    if (preSelectedSubjectId && !allowedSubjectIds.includes(String(preSelectedSubjectId))) {
        allowedSubjectIds.push(String(preSelectedSubjectId));
    }

    let addedCount = 0;

    // allSubjects vem do PHP
    allSubjects.forEach(sub => {
        // Compara IDs como Strings para evitar erros de tipo (1 vs "1")
        if (allowedSubjectIds.includes(String(sub.id))) {
            const option = document.createElement('option');
            option.value = sub.id;
            option.text = sub.name;
            
            // Seleciona se for a matéria da edição
            if (String(sub.id) === String(preSelectedSubjectId)) {
                option.selected = true;
            }
            subjectSelect.appendChild(option);
            addedCount++;
        }
    });

    if (addedCount === 0) {
        subjectSelect.disabled = true;
        subjectSelect.innerHTML = '<option value="">Este professor não tem matérias vinculadas</option>';
    } else {
        subjectSelect.disabled = false;
    }
}

// --- Ações de Abertura ---

function openAddModal(day, suggestionStart) {
    modalTitle.innerText = "Adicionar Aula";
    formAction.value = "create";
    scheduleId.value = "";
    dayOfWeekInput.value = day;
    btnDelete.style.display = "none";

    // Reseta os campos
    profSelect.value = "";
    subjectSelect.innerHTML = '<option value="">Primeiro selecione o professor</option>';
    subjectSelect.disabled = true;

    const start = formatTime(suggestionStart) || "07:00";
    startTime.value = start;
    
    try {
        let [h, m] = start.split(':').map(Number);
        let d = new Date(); d.setHours(h); d.setMinutes(m + 50);
        let eh = String(d.getHours()).padStart(2,'0');
        let em = String(d.getMinutes()).padStart(2,'0');
        if(eh >= h) endTime.value = `${eh}:${em}`; else endTime.value = "";
    } catch(e){ endTime.value = ""; }

    showModal();
}

function openEditModal(element) {
    try {
        const jsonString = element.getAttribute('data-lesson');
        const data = JSON.parse(jsonString);

        modalTitle.innerText = "Editar Aula";
        formAction.value = "update";
        scheduleId.value = data.id;
        dayOfWeekInput.value = data.day_of_week;

        startTime.value = formatTime(data.start_time);
        endTime.value = formatTime(data.end_time);

        // 1. Define o Professor
        profSelect.value = data.professor_id;
        
        // 2. Atualiza as matérias, passando o ID atual para ser pré-selecionado
        // Isso garante que o dropdown seja populado corretamente
        updateSubjects(data.subject_id);

        btnDelete.style.display = "flex"; // Ajustado para flex (botão delete)
        deleteId.value = data.id;

        showModal();
    } catch (error) {
        console.error("Erro ao abrir modal:", error);
        alert("Erro ao carregar dados da aula.");
    }
}

function deleteSchedule() {
    if (confirm("Tem certeza que deseja remover esta aula?")) {
        deleteForm.submit();
    }
}

modal.addEventListener('click', (e) => {
    if (e.target === modal) closeModal();
});