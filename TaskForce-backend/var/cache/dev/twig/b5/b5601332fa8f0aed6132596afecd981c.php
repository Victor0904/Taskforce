<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* collaborateur/show.html.twig */
class __TwigTemplate_71d52fe0594334b1cb327f2d3df9bcdb extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'title' => [$this, 'block_title'],
            'body' => [$this, 'block_body'],
        ];
    }

    protected function doGetParent(array $context): bool|string|Template|TemplateWrapper
    {
        // line 1
        return "base.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "collaborateur/show.html.twig"));

        $this->parent = $this->load("base.html.twig", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 3
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_title(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "title"));

        yield "Collaborateur";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    // line 5
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_body(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "body"));

        // line 6
        yield "    <h1>Collaborateur</h1>

    <table class=\"table\">
        <tbody>
            <tr>
                <th>Id</th>
                <td>";
        // line 12
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["collaborateur"]) || array_key_exists("collaborateur", $context) ? $context["collaborateur"] : (function () { throw new RuntimeError('Variable "collaborateur" does not exist.', 12, $this->source); })()), "id", [], "any", false, false, false, 12), "html", null, true);
        yield "</td>
            </tr>
            <tr>
                <th>Nom</th>
                <td>";
        // line 16
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["collaborateur"]) || array_key_exists("collaborateur", $context) ? $context["collaborateur"] : (function () { throw new RuntimeError('Variable "collaborateur" does not exist.', 16, $this->source); })()), "nom", [], "any", false, false, false, 16), "html", null, true);
        yield "</td>
            </tr>
            <tr>
                <th>Prenom</th>
                <td>";
        // line 20
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["collaborateur"]) || array_key_exists("collaborateur", $context) ? $context["collaborateur"] : (function () { throw new RuntimeError('Variable "collaborateur" does not exist.', 20, $this->source); })()), "prenom", [], "any", false, false, false, 20), "html", null, true);
        yield "</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>";
        // line 24
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["collaborateur"]) || array_key_exists("collaborateur", $context) ? $context["collaborateur"] : (function () { throw new RuntimeError('Variable "collaborateur" does not exist.', 24, $this->source); })()), "email", [], "any", false, false, false, 24), "html", null, true);
        yield "</td>
            </tr>
            <tr>
                <th>Poste</th>
                <td>";
        // line 28
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["collaborateur"]) || array_key_exists("collaborateur", $context) ? $context["collaborateur"] : (function () { throw new RuntimeError('Variable "collaborateur" does not exist.', 28, $this->source); })()), "poste", [], "any", false, false, false, 28), "html", null, true);
        yield "</td>
            </tr>
            <tr>
                <th>Actif</th>
                <td>";
        // line 32
        yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["collaborateur"]) || array_key_exists("collaborateur", $context) ? $context["collaborateur"] : (function () { throw new RuntimeError('Variable "collaborateur" does not exist.', 32, $this->source); })()), "actif", [], "any", false, false, false, 32)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("Yes") : ("No"));
        yield "</td>
            </tr>
            <tr>
                <th>DateNaissance</th>
                <td>";
        // line 36
        yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["collaborateur"]) || array_key_exists("collaborateur", $context) ? $context["collaborateur"] : (function () { throw new RuntimeError('Variable "collaborateur" does not exist.', 36, $this->source); })()), "dateNaissance", [], "any", false, false, false, 36)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, (isset($context["collaborateur"]) || array_key_exists("collaborateur", $context) ? $context["collaborateur"] : (function () { throw new RuntimeError('Variable "collaborateur" does not exist.', 36, $this->source); })()), "dateNaissance", [], "any", false, false, false, 36), "Y-m-d"), "html", null, true)) : (""));
        yield "</td>
            </tr>
        </tbody>
    </table>
    <h2>Compétences</h2>
<ul>
    ";
        // line 42
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, (isset($context["collaborateur"]) || array_key_exists("collaborateur", $context) ? $context["collaborateur"] : (function () { throw new RuntimeError('Variable "collaborateur" does not exist.', 42, $this->source); })()), "competences", [], "any", false, false, false, 42));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["competence"]) {
            // line 43
            yield "        <li>";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["competence"], "nom", [], "any", false, false, false, 43), "html", null, true);
            yield "</li>
    ";
            $context['_iterated'] = true;
        }
        // line 44
        if (!$context['_iterated']) {
            // line 45
            yield "        <li>Aucune compétence</li>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['competence'], $context['_parent'], $context['_iterated']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 47
        yield "</ul>

    <a href=\"";
        // line 49
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_collaborateur_index");
        yield "\">back to list</a>

    <a href=\"";
        // line 51
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_collaborateur_edit", ["id" => CoreExtension::getAttribute($this->env, $this->source, (isset($context["collaborateur"]) || array_key_exists("collaborateur", $context) ? $context["collaborateur"] : (function () { throw new RuntimeError('Variable "collaborateur" does not exist.', 51, $this->source); })()), "id", [], "any", false, false, false, 51)]), "html", null, true);
        yield "\">edit</a>

    ";
        // line 53
        yield Twig\Extension\CoreExtension::include($this->env, $context, "collaborateur/_delete_form.html.twig");
        yield "
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "collaborateur/show.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  179 => 53,  174 => 51,  169 => 49,  165 => 47,  158 => 45,  156 => 44,  149 => 43,  144 => 42,  135 => 36,  128 => 32,  121 => 28,  114 => 24,  107 => 20,  100 => 16,  93 => 12,  85 => 6,  75 => 5,  58 => 3,  41 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends 'base.html.twig' %}

{% block title %}Collaborateur{% endblock %}

{% block body %}
    <h1>Collaborateur</h1>

    <table class=\"table\">
        <tbody>
            <tr>
                <th>Id</th>
                <td>{{ collaborateur.id }}</td>
            </tr>
            <tr>
                <th>Nom</th>
                <td>{{ collaborateur.nom }}</td>
            </tr>
            <tr>
                <th>Prenom</th>
                <td>{{ collaborateur.prenom }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ collaborateur.email }}</td>
            </tr>
            <tr>
                <th>Poste</th>
                <td>{{ collaborateur.poste }}</td>
            </tr>
            <tr>
                <th>Actif</th>
                <td>{{ collaborateur.actif ? 'Yes' : 'No' }}</td>
            </tr>
            <tr>
                <th>DateNaissance</th>
                <td>{{ collaborateur.dateNaissance ? collaborateur.dateNaissance|date('Y-m-d') : '' }}</td>
            </tr>
        </tbody>
    </table>
    <h2>Compétences</h2>
<ul>
    {% for competence in collaborateur.competences %}
        <li>{{ competence.nom }}</li>
    {% else %}
        <li>Aucune compétence</li>
    {% endfor %}
</ul>

    <a href=\"{{ path('app_collaborateur_index') }}\">back to list</a>

    <a href=\"{{ path('app_collaborateur_edit', {'id': collaborateur.id}) }}\">edit</a>

    {{ include('collaborateur/_delete_form.html.twig') }}
{% endblock %}
", "collaborateur/show.html.twig", "C:\\Users\\rescue123\\Desktop\\Taskforce\\TaskForce-backend\\templates\\collaborateur\\show.html.twig");
    }
}
