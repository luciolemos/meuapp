#### ESTRUTURA BÁSICA
```
celke
├── app
│   ├── adms
│   │   ├── Controllers
│   │   │   └── Services
│   │   ├── Models
│   │   └── Viiews
│   ├── index.php
│   └── README.md
└── public
```
#### VIRTUAL HOST

.
├── app
│   ├── Controllers
│   │   ├── Admin
│   │   │   ├── DashboardController.php
│   │   │   ├── IndexController.php
│   │   │   ├── SettingsController.php
│   │   │   └── UsersController.php
│   │   └── Public
│   │       ├── ConfiguracaoController.php
│   │       ├── ContatoController.php
│   │       ├── HomeController.php
│   │       └── SobreController.php
│   ├── Core
│   │   ├── Controller.php
│   │   ├── Database.php
│   │   ├── Model.php
│   │   ├── Router.php
│   │   └── View.php
│   ├── Models
│   │   └── User.php
│   ├── README.md
│   └── Views
│       ├── Admin
│       │   ├── admin_base.twig
│       │   ├── dashboard.twig
│       │   └── partials
│       │       └── admin_navbar.twig
│       └── Public
│           ├── artigo_configuracao.twig
│           ├── base.twig
│           ├── contato.twig
│           ├── home.twig
│           ├── partials
│           │   ├── footer.twig
│           │   └── navbar.twig
│           └── sobre.twig
├── composer.json
├── composer.lock
├── config
│   ├── config.php
│   ├── routes_admin.php
│   ├── routes.php
│   └── routes_public.php
├── public
│   ├── index.php
│   └── info.php
└── vendor


Quer que eu te mostre como configurar o launch.json no VS Code pra abrir automaticamente o navegador certo (http://127.0.0.1:8080/) quando o servidor PHP embutido iniciar? Assim você roda e abre o app em um clique. 🚀


.
├── app
│   ├── Controllers
│   │   ├── Admin
│   │   │   ├── DashboardController.php
│   │   │   ├── IndexController.php
│   │   │   ├── SettingsController.php
│   │   │   └── UsersController.php
│   │   └── Public
│   │       ├── ConfiguracaoController.php
│   │       ├── ContatoController.php
│   │       ├── DescricaoProjetoController.php
│   │       ├── Documentacao
│   │       │   ├── AmbienteController.php
│   │       │   ├── BoaspraticasController.php
│   │       │   ├── BootstrapController.php
│   │       │   ├── ComposerController.php
│   │       │   ├── ConceitoController.php
│   │       │   ├── DiagnosticoController.php
│   │       │   ├── EscalabilidadeController.php
│   │       │   ├── EstruturaController.php
│   │       │   ├── HtaccessController.php
│   │       │   ├── MvcController.php
│   │       │   ├── StackController.php
│   │       │   ├── StatusController.php
│   │       │   ├── TwigController.php
│   │       │   └── VirtualhostController.php
│   │       ├── HomeController.php
│   │       ├── ManualController.php
│   │       ├── MvcController.php
│   │       ├── PdfControllerAntigo.php
│   │       ├── PdfController.php
│   │       └── SobreController.php
│   ├── Core
│   │   ├── Controller.php
│   │   ├── Database.php
│   │   ├── Model.php
│   │   ├── Router.php
│   │   └── View.php
│   ├── Models
│   │   └── User.php
│   ├── README.md
│   └── Views
│       ├── Admin
│       │   ├── admin_base.twig
│       │   ├── dashboard.twig
│       │   └── partials
│       │       └── admin_navbar.twig
│       └── Public
│           ├── artigo_configuracao.twig
│           ├── artigo_mvc.twig
│           ├── base.twig
│           ├── contato.twig
│           ├── descricao_projeto.twig
│           ├── documentacao
│           │   ├── ambiente.twig
│           │   ├── boaspraticas.twig
│           │   ├── bootstrap.twig
│           │   ├── composer.twig
│           │   ├── conceito.twig
│           │   ├── diagnostico.twig
│           │   ├── escalabilidade.twig
│           │   ├── estrutura.twig
│           │   ├── htaccess.twig
│           │   ├── mvc.twig
│           │   ├── partials
│           │   │   ├── cover.twig
│           │   │   ├── footer.twig
│           │   │   ├── header.twig
│           │   │   └── toc.twig
│           │   ├── stack.twig
│           │   ├── status.twig
│           │   ├── twig.twig
│           │   └── virtualhost.twig
│           ├── errors
│           │   ├── 403.twig
│           │   ├── 404.twig
│           │   └── 500.twig
│           ├── fonts
│           │   ├── fa-regular-400.ttf
│           │   └── fa-solid-900.ttf
│           ├── home.twig
│           ├── images
│           │   └── logo.png
│           ├── manual.twig
│           ├── partials
│           │   ├── footer.twig
│           │   ├── macros.twig
│           │   └── navbar.twig
│           └── sobre.twig
├── composer.json
├── composer.lock
├── config
│   ├── config.php
│   ├── routes_admin.php
│   ├── routes.php
│   └── routes_public.php
├── estrutura.txt
├── public
│   ├── css
│   │   ├── pdf-style.css
│   │   └── style.css
│   ├── fonts
│   ├── images
│   │   ├── logo.jpg
│   │   └── logo.png
│   ├── img
│   │   └── logo.png
│   ├── index.php
│   ├── info.php
│   └── js
│       └── app.js
├── storage
│   └── fonts
└── vendor