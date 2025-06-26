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

/* mission/show.html.twig */
class __TwigTemplate_f1a56d30334be88983e2275b1f8f5fbb extends Template
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
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "mission/show.html.twig"));

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

        yield "Mission";
        
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
        yield "    <h1>Mission</h1>

    <table class=\"table\">
        <tbody>
            <tr>
                <th>Id</th>
                <td>";
        // line 12
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["mission"]) || array_key_exists("mission", $context) ? $context["mission"] : (function () { throw new RuntimeError('Variable "mission" does not exist.', 12, $this->source); })()), "id", [], "any", false, false, false, 12), "html", null, true);
        yield "</td>
            </tr>
            <tr>
                <th>Titre</th>
                <td>";
        // line 16
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["mission"]) || array_key_exists("mission", $context) ? $context["mission"] : (function () { throw new RuntimeError('Variable "mission" does not exist.', 16, $this->source); })()), "titre", [], "any", false, false, false, 16), "html", null, true);
        yield "</td>
            </tr>
            <tr>
                <th>Description</th>
                <td>";
        // line 20
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["mission"]) || array_key_exists("mission", $context) ? $context["mission"] : (function () { throw new RuntimeError('Variable "mission" does not exist.', 20, $this->source); })()), "description", [], "any", false, false, false, 20), "html", null, true);
        yield "</td>
            </tr>
            <tr>
                <th>Date_debut</th>
                <td>";
        // line 24
        yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["mission"]) || array_key_exists("mission", $context) ? $context["mission"] : (function () { throw new RuntimeError('Variable "mission" does not exist.', 24, $this->source); })()), "dateDebut", [], "any", false, false, false, 24)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, (isset($context["mission"]) || array_key_exists("mission", $context) ? $context["mission"] : (function () { throw new RuntimeError('Variable "mission" does not exist.', 24, $this->source); })()), "dateDebut", [], "any", false, false, false, 24), "Y-m-d"), "html", null, true)) : (""));
        yield "</td>
            </tr>
            <tr>
                <th>Date_fin</th>
                <td>";
        // line 28
        yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["mission"]) || array_key_exists("mission", $context) ? $context["mission"] : (function () { throw new RuntimeError('Variable "mission" does not exist.', 28, $this->source); })()), "dateFin", [], "any", false, false, false, 28)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, (isset($context["mission"]) || array_key_exists("mission", $context) ? $context["mission"] : (function () { throw new RuntimeError('Variable "mission" does not exist.', 28, $this->source); })()), "dateFin", [], "any", false, false, false, 28), "Y-m-d"), "html", null, true)) : (""));
        yield "</td>
            </tr>
            <tr>
                <th>Nom</th>
                <td>";
        // line 32
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["mission"]) || array_key_exists("mission", $context) ? $context["mission"] : (function () { throw new RuntimeError('Variable "mission" does not exist.', 32, $this->source); })()), "nom", [], "any", false, false, false, 32), "html", null, true);
        yield "</td>
            </tr>
        </tbody>
    </table>
    <h2>Compétences requises</h2>
<ul>
    ";
        // line 38
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, (isset($context["mission"]) || array_key_exists("mission", $context) ? $context["mission"] : (function () { throw new RuntimeError('Variable "mission" does not exist.', 38, $this->source); })()), "competences", [], "any", false, false, false, 38));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["competence"]) {
            // line 39
            yield "        <li>";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["competence"], "nom", [], "any", false, false, false, 39), "html", null, true);
            yield "</li>
    ";
            $context['_iterated'] = true;
        }
        // line 40
        if (!$context['_iterated']) {
            // line 41
            yield "        <li>Aucune compétence requise</li>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['competence'], $context['_parent'], $context['_iterated']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 43
        yield "</ul>

    <a href=\"";
        // line 45
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_mission_index");
        yield "\">back to list</a>

    <a href=\"";
        // line 47
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_mission_edit", ["id" => CoreExtension::getAttribute($this->env, $this->source, (isset($context["mission"]) || array_key_exists("mission", $context) ? $context["mission"] : (function () { throw new RuntimeError('Variable "mission" does not exist.', 47, $this->source); })()), "id", [], "any", false, false, false, 47)]), "html", null, true);
        yield "\">edit</a>

    ";
        // line 49
        yield Twig\Extension\CoreExtension::include($this->env, $context, "mission/_delete_form.html.twig");
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
        return "mission/show.html.twig";
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
        return array (  172 => 49,  167 => 47,  162 => 45,  158 => 43,  151 => 41,  149 => 40,  142 => 39,  137 => 38,  128 => 32,  121 => 28,  114 => 24,  107 => 20,  100 => 16,  93 => 12,  85 => 6,  75 => 5,  58 => 3,  41 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends 'base.html.twig' %}

{% block title %}Mission{% endblock %}

{% block body %}
    <h1>Mission</h1>

    <table class=\"table\">
        <tbody>
            <tr>
                <th>Id</th>
                <td>{{ mission.id }}</td>
            </tr>
            <tr>
                <th>Titre</th>
                <td>{{ mission.titre }}</td>
            </tr>
            <tr>
                <th>Description</th>
                <td>{{ mission.description }}</td>
            </tr>
            <tr>
                <th>Date_debut</th>
                <td>{{ mission.dateDebut ? mission.dateDebut|date('Y-m-d') : '' }}</td>
            </tr>
            <tr>
                <th>Date_fin</th>
                <td>{{ mission.dateFin ? mission.dateFin|date('Y-m-d') : '' }}</td>
            </tr>
            <tr>
                <th>Nom</th>
                <td>{{ mission.nom }}</td>
            </tr>
        </tbody>
    </table>
    <h2>Compétences requises</h2>
<ul>
    {% for competence in mission.competences %}
        <li>{{ competence.nom }}</li>
    {% else %}
        <li>Aucune compétence requise</li>
    {% endfor %}
</ul>

    <a href=\"{{ path('app_mission_index') }}\">back to list</a>

    <a href=\"{{ path('app_mission_edit', {'id': mission.id}) }}\">edit</a>

    {{ include('mission/_delete_form.html.twig') }}
{% endblock %}
", "mission/show.html.twig", "C:\\Users\\rescue123\\Desktop\\Taskforce\\TaskForce-backend\\templates\\mission\\show.html.twig");
    }
}
