<?= $this->extend('layout/principal'); ?>
<?= $this->section('conteudo'); ?>

<?php
// --- LÓGICA DE ORDENAÇÃO E URL ---
$currentOrdem   = isset($ordem) ? $ordem : 'id';
$currentDirecao = isset($direcao) ? $direcao : 'asc';

$proximaDirId   = ($currentOrdem === 'id' && $currentDirecao === 'asc') ? 'desc' : 'asc';
$proximaDirNome = ($currentOrdem === 'nome' && $currentDirecao === 'asc') ? 'desc' : 'asc';

// Links para ordenação
$linkId   = site_url("pessoas?ordem=id&direcao={$proximaDirId}");
$linkNome = site_url("pessoas?ordem=nome&direcao={$proximaDirNome}");

// Helper para definir o ícone correto do Font Awesome
function getIconeOrdenacao($colunaAtual, $colunaAlvo, $direcao)
{
    if ($colunaAtual !== $colunaAlvo) {
        return '<i class="fas fa-sort has-text-grey-light ml-1"></i>'; // Neutro
    }
    return ($direcao === 'asc')
        ? '<i class="fas fa-sort-up has-text-link ml-1"></i>'
        : '<i class="fas fa-sort-down has-text-link ml-1"></i>';
}

$iconeId   = getIconeOrdenacao($currentOrdem, 'id', $currentDirecao);
$iconeNome = getIconeOrdenacao($currentOrdem, 'nome', $currentDirecao);

// Informações de paginação
$currentPage = $pager->getCurrentPage();
$totalPages = $pager->getPageCount();
$totalRecords = $pager->getTotal();

// Função para gerar paginação personalizada com Bulma
function paginacaoBulma($pager, $currentOrdem, $currentDirecao)
{
    $html = '<nav class="pagination is-centered" role="navigation" aria-label="pagination">';

    // Botão anterior
    $previousURI = $pager->getPreviousPageURI();
    if ($previousURI) {
        $previousURI = adicionarOrdenacaoURI($previousURI, $currentOrdem, $currentDirecao);
        $html .= '<a href="' . $previousURI . '" class="pagination-previous">Anterior</a>';
    } else {
        $html .= '<a class="pagination-previous" disabled>Anterior</a>';
    }

    // Botão próximo
    $nextURI = $pager->getNextPageURI();
    if ($nextURI) {
        $nextURI = adicionarOrdenacaoURI($nextURI, $currentOrdem, $currentDirecao);
        $html .= '<a href="' . $nextURI . '" class="pagination-next">Próxima</a>';
    } else {
        $html .= '<a class="pagination-next" disabled>Próxima</a>';
    }

    $html .= '<ul class="pagination-list">';

    // Links de página
    $links = $pager->links();
    if (is_string($links)) {
        // Se for string, extraímos os links
        preg_match_all('/<a[^>]*href=["\']([^"\']*)["\'][^>]*>([^<]*)<\/a>/', $links, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $href = $match[1];
            $text = $match[2];
            $href = adicionarOrdenacaoURI($href, $currentOrdem, $currentDirecao);

            $isCurrent = (strpos($match[0], 'active') !== false);
            $class = $isCurrent ? 'pagination-link is-current' : 'pagination-link';

            $html .= '<li><a href="' . $href . '" class="' . $class . '" aria-label="Página ' . $text . '">' . $text . '</a></li>';
        }
    }

    $html .= '</ul>';
    $html .= '</nav>';

    return $html;
}

// Função para adicionar parâmetros de ordenação aos URIs de paginação
function adicionarOrdenacaoURI($uri, $ordem, $direcao)
{
    if (empty($ordem) || empty($direcao)) {
        return $uri;
    }

    // Verificar se a URI já tem parâmetros de query
    if (strpos($uri, '?') === false) {
        $uri .= '?';
    } else {
        $uri .= '&';
    }

    $uri .= 'ordem=' . urlencode($ordem) . '&direcao=' . urlencode($direcao);
    return $uri;
}
?>

<div class="container">

    <div class="level mb-5">
        <div class="level-left">
            <div>
                <h1 class="title is-3 has-text-grey-darker mb-2">
                    <?= esc($titulo ?? 'Lista de Pessoas') ?>
                </h1>
                <p class="subtitle is-6 has-text-grey">Gerencie seus clientes e fornecedores</p>
            </div>
        </div>
        <div class="level-right">
            <a href="<?= site_url('pessoas/nova') ?>" class="button is-link">
                <span class="icon">
                    <i class="fas fa-plus"></i>
                </span>
                <span>Nova Pessoa</span>
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('sucesso')): ?>
        <div class="notification is-success is-light">
            <button class="delete"></button>
            <?= session()->getFlashdata('sucesso') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('erro')): ?>
        <div class="notification is-danger is-light">
            <button class="delete"></button>
            <?= session()->getFlashdata('erro') ?>
        </div>
    <?php endif; ?>

    <div class="box">
        <?php if (empty($pessoas)): ?>

            <div class="has-text-centered py-6">
                <span class="icon is-large has-text-grey-light mb-3">
                    <i class="fas fa-users-slash fa-3x"></i>
                </span>
                <p class="is-size-5 has-text-grey">Nenhuma pessoa cadastrada ainda.</p>
            </div>

        <?php else: ?>

            <div class="table-container">
                <table class="table is-fullwidth is-striped is-hoverable is-vcentered">
                    <thead>
                        <tr>
                            <th>
                                <a href="<?= $linkId ?>" class="has-text-grey-darker is-flex is-align-items-center" title="Ordenar por ID">
                                    ID <?= $iconeId ?>
                                </a>
                            </th>
                            <th>
                                <a href="<?= $linkNome ?>" class="has-text-grey-darker is-flex is-align-items-center" title="Ordenar por Nome">
                                    NOME <?= $iconeNome ?>
                                </a>
                            </th>
                            <th>Documento</th>
                            <th>E-mail</th>
                            <th class="has-text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pessoas as $pessoa): ?>
                            <tr>
                                <td class="has-text-grey"><?= esc($pessoa['id']) ?></td>
                                <td class="has-text-weight-medium text-dark">
                                    <?= esc($pessoa['nome']) ?>
                                    <?php if (isset($pessoa['tipo_pessoa'])): ?>
                                        <span class="tag is-white is-small text-gray-500 border ml-2">
                                            <?= esc($pessoa['tipo_pessoa']) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="tag is-light is-small has-text-weight-bold mr-1">
                                        <?= esc($pessoa['tipo_documento']) ?>
                                    </span>
                                    <span class="is-family-monospace"><?= esc($pessoa['documento']) ?></span>
                                </td>
                                <td><?= esc($pessoa['email']) ?></td>
                                <td class="has-text-right">
                                    <div class="buttons is-right are-small">

                                        <a href="<?= site_url('pessoas/editar/' . $pessoa['id']) ?>" class="button is-info is-outlined" title="Editar">
                                            <span class="icon">
                                                <i class="fas fa-edit"></i>
                                            </span>
                                        </a>

                                        <form action="<?= site_url('pessoas/excluir/' . $pessoa['id']) ?>" method="post" class="is-inline js-form-delete">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="_method" value="DELETE">

                                            <button type="submit" class="button is-danger is-outlined" title="Excluir">
                                                <span class="icon">
                                                    <i class="fas fa-trash"></i>
                                                </span>
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>
    </div>

    <?php if (!empty($pessoas) && $pager->getPageCount() > 1): ?>
        <div class="section pt-0">
            <div class="level">
                <div class="level-left">
                    <div class="level-item">
                        <div class="has-text-grey">
                            <span class="icon is-small">
                                <i class="fas fa-info-circle"></i>
                            </span>
                            <span class="ml-1">
                                Mostrando <?= $pager->getPerPage() ?> de <?= $totalRecords ?> registros
                                (Página <?= $currentPage ?> de <?= $totalPages ?>)
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paginação estilizada com Bulma -->
            <div class="mt-4">
                <?php echo paginacaoBulma($pager, $currentOrdem, $currentDirecao); ?>
            </div>
        </div>
    <?php endif; ?>

</div>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Botões de fechar notificações
        document.querySelectorAll('.notification .delete').forEach(function(button) {
            button.addEventListener('click', function() {
                this.parentElement.style.display = 'none';
            });
        });

        // Confirmação para exclusão
        document.querySelectorAll('.js-form-delete').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                if (!confirm('Tem certeza que deseja excluir esta pessoa?')) {
                    e.preventDefault();
                }
            });
        });

        // Adicionar ícones aos botões de paginação
        const pagination = document.querySelector('.pagination');
        if (pagination) {
            const previousBtn = pagination.querySelector('.pagination-previous:not([disabled])');
            const nextBtn = pagination.querySelector('.pagination-next:not([disabled])');

            if (previousBtn) {
                previousBtn.innerHTML = '<span class="icon"><i class="fas fa-chevron-left"></i></span><span>Anterior</span>';
            }

            if (nextBtn) {
                nextBtn.innerHTML = '<span>Próxima</span><span class="icon"><i class="fas fa-chevron-right"></i></span>';
            }
        }
    });
</script>
<?php $this->endSection(); ?>