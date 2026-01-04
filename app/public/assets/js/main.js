document.addEventListener('DOMContentLoaded', () => {

    // 1. Funcionalidade do Menu Mobile (Hambúrguer) do Bulma
    const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);

    if ($navbarBurgers.length > 0) {
        $navbarBurgers.forEach(el => {
            el.addEventListener('click', () => {
                // Pega o alvo do atributo "data-target"
                const target = el.dataset.target;
                const $target = document.getElementById(target);

                // Alterna a classe "is-active" no ícone e no menu
                el.classList.toggle('is-active');
                $target.classList.toggle('is-active');
            });
        });
    }

    // 2. Funcionalidade para fechar as notificações (Botão X)
    (document.querySelectorAll('.notification .delete') || []).forEach(($delete) => {
        const $notification = $delete.parentNode;

        $delete.addEventListener('click', () => {
            $notification.parentNode.removeChild($notification);
        });
    });

    // 3. Funcionalidade de Confirmação de Exclusão (Genérico)
const deleteForms = document.querySelectorAll('.js-form-delete');

if (deleteForms.length > 0) {
    deleteForms.forEach(form => {
        form.addEventListener('submit', (e) => {
            // Cria a mensagem de confirmação
            const confirmation = confirm('Tem certeza que deseja excluir este registro? Esta ação não pode ser desfeita.');
            
            // Se o usuário clicar em "Cancelar" (false), impede o envio do form
            if (!confirmation) {
                e.preventDefault();
            }
        });
    });
}
});