<div class="modal-overlay" id="policyModal">
    <div class="modal-card" style="max-width: 700px;"> <div class="modal-header">
            <h3>Política de Privacidade e Proteção de Dados</h3>
            <i class='bx bx-x close-modal' onclick="closePolicyModal()"></i>
        </div>
        
        <div style="max-height: 60vh; overflow-y: auto; padding-right: 15px; color: #555; line-height: 1.6; font-size: 0.95rem; text-align: left;">
            
            <p style="margin-bottom: 15px;"><strong>Última atualização: Novembro de 2025</strong></p>
            
            <p style="margin-bottom: 15px;">A Blome preza pela privacidade e segurança das informações de estudantes, professores e gestores escolares. Esta Política de Privacidade descreve como coletamos, usamos, armazenamos e protegemos os dados pessoais, em conformidade com a Lei Geral de Proteção de Dados Pessoais (Lei nº 13.709/2018 - LGPD) e as diretrizes das lojas de aplicativos referentes ao uso de APIs sensíveis.</p>
            
            <p style="margin-bottom: 20px;">Ao utilizar o aplicativo Blome e nossa plataforma web, você concorda com as práticas descritas nesta política.</p>

            <h4 style="color: #333; margin-bottom: 10px;">1. Informações Gerais</h4>
            <p style="margin-bottom: 20px;">A Blome é uma plataforma de gestão escolar focada na melhoria do desempenho pedagógico através do gerenciamento do uso de dispositivos móveis em sala de aula. Nosso objetivo é garantir um ambiente de aprendizado livre de distrações digitais, oferecendo ferramentas para que instituições de ensino gerenciem o acesso a aplicativos nos dispositivos dos alunos durante o horário escolar.</p>

            <h4 style="color: #333; margin-bottom: 10px;">2. Coleta de Dados Pessoais</h4>
            <p>A coleta de dados ocorre de duas formas principais:</p>
            <ul style="margin-bottom: 20px; padding-left: 20px; list-style-type: disc;">
                <li><strong>Cadastro Administrativo:</strong> Dados inseridos pelos administradores da instituição de ensino ao registrar alunos e professores na plataforma web.</li>
                <li><strong>Uso do Aplicativo Móvel:</strong> Dados técnicos coletados automaticamente pelo aplicativo instalado no dispositivo do aluno para viabilizar o bloqueio.</li>
            </ul>

            <h4 style="color: #333; margin-bottom: 15px;">3. Tipos de Dados Coletados</h4>
            <p>Para o funcionamento adequado dos serviços, tratamos as seguintes categorias de dados:</p>
            
            <p><strong>A. Dados de Cadastro (Web)</strong></p>
            <ul style="margin-bottom: 10px; padding-left: 20px; list-style-type: circle;">
                <li><strong>Identificação:</strong> Nome completo.</li>
                <li><strong>Contato e Login:</strong> Endereço de e-mail e senha (armazenada de forma criptografada via provedor de autenticação) cadastrados pelo administrador.</li>
                <li><strong>Vínculo Institucional:</strong> Identificação da instituição, turma/classe e tipo de usuário (aluno, professor ou administrador).</li>
            </ul>

            <p><strong>B. Dados do Dispositivo (Aplicativo Móvel)</strong></p>
            <ul style="margin-bottom: 20px; padding-left: 20px; list-style-type: circle;">
                <li><strong>Lista de Aplicativos Instalados:</strong> O aplicativo coleta a lista de aplicativos instalados no dispositivo (Nome do pacote, Nome do App e Ícone) para permitir que a gestão escolar selecione quais devem ser bloqueados.</li>
                <li><strong>Status da Tela:</strong> Monitoramento da janela ativa para identificar qual aplicativo está sendo utilizado em primeiro plano.</li>
            </ul>

            <h4 style="color: #333; margin-bottom: 10px;">4. Uso do Serviço de Acessibilidade (Android)</h4>
            <p style="background: #fff3cd; color: #856404; padding: 10px; border-radius: 8px; margin-bottom: 10px; font-size: 0.9rem;">
                <strong>Importante:</strong> O aplicativo Blome utiliza a API de Acessibilidade do Android (<code>AccessibilityService</code>).
            </p>
            <ul style="margin-bottom: 20px; padding-left: 20px; list-style-type: disc;">
                <li style="margin-bottom: 8px;"><strong>Finalidade Específica:</strong> O serviço é utilizado exclusivamente para detectar quando um aplicativo bloqueado é aberto pelo aluno. Ao identificar um aplicativo não autorizado (listado em nossa base local de bloqueios), o sistema executa automaticamente a ação de retornar à tela inicial (<code>GLOBAL_ACTION_HOME</code>), impedindo o acesso.</li>
                <li><strong>Limitação de Acesso:</strong> O Serviço de Acessibilidade da Blome <strong>NÃO</strong> coleta, lê, armazena ou compartilha conteúdo pessoal exibido na tela (como mensagens, senhas, dados bancários ou fotos). Seu uso é estritamente restrito à leitura do aplicativo iniciado na janela ativa para comparação com a lista de bloqueio.</li>
            </ul>

            <h4 style="color: #333; margin-bottom: 10px;">5. Finalidade do Tratamento de Dados</h4>
            <ul style="margin-bottom: 20px; padding-left: 20px; list-style-type: disc;">
                <li><strong>Gestão Pedagógica:</strong> Permitir que a escola administre turmas e vincule alunos e professores.</li>
                <li><strong>Controle de Foco:</strong> Executar o bloqueio técnico de aplicativos, com ênfase em apps que distraem o aluno durante a aula, como jogos e redes sociais, conforme configurado pela escola.</li>
                <li><strong>Autenticação:</strong> Garantir que apenas usuários autorizados pela instituição de ensino e autenticados em nosso banco de dados acessem a plataforma.</li>
            </ul>

            <h4 style="color: #333; margin-bottom: 10px;">6. Armazenamento e Segurança</h4>
            <ul style="margin-bottom: 20px; padding-left: 20px; list-style-type: disc;">
                <li><strong>Banco de Dados:</strong> As informações de cadastro (nome, e-mail, turma) são armazenadas em servidores seguros na nuvem (Supabase), protegidos por autenticação e criptografia disponibilizados pela plataforma do Supabase.</li>
                <li><strong>Armazenamento Local:</strong> A lista de aplicativos bloqueados é armazenada localmente no dispositivo do aluno (SharedPreferences) para garantir o funcionamento do bloqueio mesmo sem conexão constante com a internet.</li>
                <li><strong>Não Retenção de Monitoramento:</strong> Não armazenamos histórico de navegação ou uso de aplicativos. O monitoramento é feito em tempo real apenas para a decisão instantânea de "bloquear ou permitir", sem gerar logs persistentes de atividade do usuário no servidor.</li>
            </ul>

            <h4 style="color: #333; margin-bottom: 10px;">7. Compartilhamento de Dados</h4>
            <p>A Blome não vende nem comercializa dados pessoais. O compartilhamento ocorre apenas nas seguintes situações:</p>
            <ul style="margin-bottom: 20px; padding-left: 20px; list-style-type: disc;">
                <li><strong>Instituição de Ensino:</strong> Os gestores da escola têm acesso aos dados de cadastro e configurações de bloqueio dos alunos vinculados à sua instituição.</li>
                <li><strong>Obrigação Legal:</strong> Mediante requisição judicial ou para cumprimento de normas legais aplicáveis ao setor educacional.</li>
            </ul>

            <h4 style="color: #333; margin-bottom: 10px;">8. Tecnologias de Sessão</h4>
            <p style="margin-bottom: 20px;">Utilizamos sessões PHP e tokens de autenticação para gerenciar o login seguro dos usuários na plataforma web, garantindo que a conta permaneça ativa durante a navegação.</p>

            <h4 style="color: #333; margin-bottom: 10px;">9. Direitos dos Titulares (LGPD)</h4>
            <p>Em conformidade com a LGPD, os usuários (alunos, responsáveis e professores) têm direito a:</p>
            <ul style="margin-bottom: 20px; padding-left: 20px; list-style-type: disc;">
                <li>Solicitar a confirmação da existência de tratamento de dados.</li>
                <li>Acessar os dados mantidos pela Blome.</li>
                <li>Solicitar a correção de dados incompletos, inexatos ou desatualizados.</li>
                <li>Solicitar a exclusão de dados, exceto quando a manutenção for necessária para cumprimento de obrigação legal ou contratual com a instituição de ensino.</li>
            </ul>

            <h4 style="color: #333; margin-bottom: 10px;">10. Alterações nesta Política</h4>
            <p style="margin-bottom: 20px;">Esta Política de Privacidade pode ser atualizada para refletir melhorias na plataforma ou mudanças na legislação. As alterações entrarão em vigor assim que publicadas em nosso site ou aplicativo. Recomendamos a revisão periódica deste documento.</p>

            <h4 style="color: #333; margin-bottom: 10px;">11. Contato</h4>
            <p style="margin-bottom: 20px;">Para exercer seus direitos de titular ou esclarecer dúvidas sobre esta Política de Privacidade, entre em contato conosco através do email privacida.blome@outlook.com, ou com a administração de sua instituição de ensino.</p>
            
            <p><em>Este documento é parte integrante dos Termos de Uso da plataforma Blome.</em></p>
        </div>

        <div class="modal-actions" style="margin-top: 20px;">
            <button type="button" class="btn-save-modal" onclick="closePolicyModal()">Entendido</button>
        </div>
    </div>
</div>