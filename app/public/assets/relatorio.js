        // Adicionar confirmação ao exportar
        document.querySelectorAll('a[href*="exportar"]').forEach(link => {
            link.addEventListener('click', function(e) {
                if (!confirm('Deseja exportar os dados?')) {
                    e.preventDefault();
                }
            });
        });
