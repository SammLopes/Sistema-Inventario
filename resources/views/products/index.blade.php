{{-- resources/views/products/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Lista de Produtos')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Sistema de Estoque</h1>
                <div>
                    <span class="badge bg-secondary me-2">{{ $products->total() }} produtos</span>
                    <button type="button" class="btn btn-primary" onclick="syncWithApi()">
                        <i class="fas fa-sync-alt"></i> Sincronizar com API
                    </button>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fas fa-sign-out-alt"></i> Sair
                        </button>
                    </form>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Produtos Cadastrados</h5>
                    <form method="GET" action="{{ route('products.index') }}" id="filter-form" class="flex items-center gap-2 mb-3">
                         <div class="d-flex align-items-center mt-3">
                            <label for="category" class="form-label mb-0 me-2">Categoria:</label>
                            <select name="category" id="category" class="form-select form-select-sm" style="width: 150px"
                                    onchange="document.getElementById('filter-form').submit()">
                                <option value="">Todas</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ ($category === $cat) ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-flex align-items-center mt-3">
                            <label for="q" class="form-label mb-0 me-2">Buscar:</label>
                            <input type="text" name="q" id="q" value="{{ $q }}" 
                                class="form-control form-control-sm" 
                                style="width: 200px;" placeholder="Nome do produto">
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm mt-1">Filtrar</button>

                        @if($category || $q)
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm mt-1">Limpar</a>
                        @endif
                    </form>
                </div>
                <div class="card-body p-0">
                    @if($products->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="60">Imagem</th>
                                        <th>Produto</th>
                                        <th>Categoria</th>
                                        <th>Preço</th>
                                        <th width="120">Estoque</th>
                                        <th width="100">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                        <tr>
                                            <td>
                                                @if($product->image)
                                                    <img src="{{ $product->image }}" 
                                                         alt="{{ $product->name }}" 
                                                         class="img-thumbnail" 
                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                         style="width: 50px; height: 50px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $product->name }}</strong>
                                                @if($product->description)
                                                    <br><small class="text-muted">{{ Str::limit($product->description, 60) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($product->category)
                                                    <span class="badge bg-info">{{ ucfirst($product->category) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong class="text-success">{{ $product->preco_formatado }}</strong>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <input type="number" 
                                                           class="form-control" 
                                                           value="{{ $product->stock }}" 
                                                           id="estoque_{{ $product->id }}"
                                                           min="0">
                                                </div>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-success" 
                                                        onclick="updateStock({{ $product->id }})">
                                                    <i class="fas fa-save"></i>
                                                </button>
                                                <button 
                                                        type="button"
                                                        class="btn btn-sm btn-danger" 
                                                        onclick="removeStock({{ $product->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                            
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        @if($hasAnyProducts && ($category || $q))
                            <div class="text-center py-5">
                                <div class="mb-2">
                                    {{-- seu ícone opcional --}}
                                    <i class="bi bi-search" style="font-size: 2rem;"></i>
                                </div>
                                <h5>Nenhum produto encontrado</h5>
                                <p class="text-muted mb-3">
                                    Ajuste os filtros de <strong>Categoria</strong> e/ou <strong>Buscar</strong>.
                                </p>
                                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
                                    Limpar filtros
                                </a>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="mb-2">
                                    {{-- seu ícone atual da caixinha --}}
                                    <i class="bi bi-box-seam" style="font-size: 2rem;"></i>
                                </div>
                                <h5>Nenhum produto cadastrado</h5>
                                <p class="text-muted mb-3">
                                    Clique em <strong>“Sincronizar com API”</strong> para importar produtos.
                                </p>
                            </div>
                        @endif
                    @endif
                </div>

                <div class="d-flex justify-content-end align-items-center mt-3 ml-1 mr-1 " >
                    
                 <div >
                    {{ $products->links('pagination::bootstrap-5') }}
                </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status"></div>
                <p class="mb-0">Sincronizando produtos...</p>
            </div>
        </div>
    </div>
</div>

<script>

function removeStock(productId){

    fetch(`/produtos/${productId}/estoque`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log(" TEste ", data);
        if (data.success) {

            window.location.reload();   
            showToast(data.message, 'success');
        } else {
            showToast('Erro ao remover do estoque', 'error');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showToast('Erro ao remover do estoque', 'error');
    });
}

// Atualizar estoque
function updateStock(productId) {
    const estoque = document.getElementById(`estoque_${productId}`).value;
    
    fetch(`/produtos/${productId}/estoque`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ stock: parseInt(estoque) })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {

            showToast(data.message, 'success');

        } else {
            showToast('Erro ao atualizar estoque', 'error');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showToast('Erro ao atualizar estoque', 'error');
    });
}

// Sincronizar com API
function syncWithApi() {
    const modal = new bootstrap.Modal(document.getElementById('loadingModal'));
    modal.show();
    
    fetch('/produtos/sync-api', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => {
        modal.hide();
        window.location.reload();
    })
    .catch(error => {
        console.error('Erro:', error);
        modal.hide();
        showToast('Erro ao sincronizar produtos', 'error');
    });
}

// Mostrar toast
function showToast(message, type = 'success') {
     
    const container = document.getElementById('toast-container') || document.body;

    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    toast.innerHTML = `
        <div class="d-flex">
        <div class="toast-body">${message}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fechar"></button>
        </div>
    `;
    
    container.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast, { autohide: true, delay: 3000 });
    bsToast.show();
    
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}
</script>
@endsection