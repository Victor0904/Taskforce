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

/* mission/index.html.twig */
class __TwigTemplate_bc072ffe4cc34260ca6d44b87c952bfb extends Template
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
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "mission/index.html.twig"));

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

        yield "Mission index";
        
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
        yield "    <h1>Mission index</h1>

    <table class=\"table\">
        <thead>
            <tr>
                <th>Id</th>
                <th>Titre</th>
                <th>Description</th>
                <th>Date_debut</th>
                <th>Date_fin</th>
                <th>Nom</th>
                <th>Compétences</th> ";
        // line 18
        yield "                <th>actions</th>
            </tr>
        </thead>
        <tbody>
        ";
        // line 22
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["missions"]) || array_key_exists("missions", $context) ? $context["missions"] : (function () { throw new RuntimeError('Variable "missions" does not exist.', 22, $this->source); })()));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["mission"]) {
            // line 23
            yield "            <tr>
                <td>";
            // line 24
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["mission"], "id", [], "any", false, false, false, 24), "html", null, true);
            yield "</td>
                <td>";
            // line 25
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["mission"], "titre", [], "any", false, false, false, 25), "html", null, true);
            yield "</td>
                <td>";
            // line 26
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["mission"], "description", [], "any", false, false, false, 26), "html", null, true);
            yield "</td>
                <td>";
            // line 27
            yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["mission"], "dateDebut", [], "any", false, false, false, 27)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, $context["mission"], "dateDebut", [], "any", false, false, false, 27), "Y-m-d"), "html", null, true)) : (""));
            yield "</td>
                <td>";
            // line 28
            yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["mission"], "dateFin", [], "any", false, false, false, 28)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, $context["mission"], "dateFin", [], "any", false, false, false, 28), "Y-m-d"), "html", null, true)) : (""));
            yield "</td>
                <td>";
            // line 29
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["mission"], "nom", [], "any", false, false, false, 29), "html", null, true);
            yield "</td>
                <td>
                    ";
            // line 31
            if ((($tmp =  !Twig\Extension\CoreExtension::testEmpty(CoreExtension::getAttribute($this->env, $this->source, $context["mission"], "competences", [], "any", false, false, false, 31))) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 32
                yield "                        <ul>
                            ";
                // line 33
                $context['_parent'] = $context;
                $context['_seq'] = CoreExtension::ensureTraversable(CoreExtension::getAttribute($this->env, $this->source, $context["mission"], "competences", [], "any", false, false, false, 33));
                foreach ($context['_seq'] as $context["_key"] => $context["comp"]) {
                    // line 34
                    yield "                                <li>";
                    yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["comp"], "nom", [], "any", false, false, false, 34), "html", null, true);
                    yield "</li>
                            ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_key'], $context['comp'], $context['_parent']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 36
                yield "                        </ul>
                    ";
            } else {
                // line 38
                yield "                        <em>Aucune compétence</em>
                    ";
            }
            // line 40
            yield "                </td>
                <td>
                    <a href=\"";
            // line 42
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_mission_show", ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["mission"], "id", [], "any", false, false, false, 42)]), "html", null, true);
            yield "\">show</a>
                    <a href=\"";
            // line 43
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_mission_edit", ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["mission"], "id", [], "any", false, false, false, 43)]), "html", null, true);
            yield "\">edit</a>
                </td>
            </tr>
        ";
            $context['_iterated'] = true;
        }
        // line 46
        if (!$context['_iterated']) {
            // line 47
            yield "            <tr>
                <td colspan=\"8\">no records found</td>
            </tr>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['mission'], $context['_parent'], $context['_iterated']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 51
        yield "        </tbody>
    </table>
    

    <a href=\"";
        // line 55
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_mission_new");
        yield "\">Create new</a>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "mission/index.html.twig";
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
        return array (  196 => 55,  190 => 51,  181 => 47,  179 => 46,  171 => 43,  167 => 42,  163 => 40,  159 => 38,  155 => 36,  146 => 34,  142 => 33,  139 => 32,  137 => 31,  132 => 29,  128 => 28,  124 => 27,  120 => 26,  116 => 25,  112 => 24,  109 => 23,  104 => 22,  98 => 18,  85 => 6,  75 => 5,  58 => 3,  41 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends 'base.html.twig' %}

{% block title %}Mission index{% endblock %}

{% block body %}
    <h1>Mission index</h1>

    <table class=\"table\">
        <thead>
            <tr>
                <th>Id</th>
                <th>Titre</th>
                <th>Description</th>
                <th>Date_debut</th>
                <th>Date_fin</th>
                <th>Nom</th>
                <th>Compétences</th> {# Nouvelle colonne #}
                <th>actions</th>
            </tr>
        </thead>
        <tbody>
        {% for mission in missions %}
            <tr>
                <td>{{ mission.id }}</td>
                <td>{{ mission.titre }}</td>
                <td>{{ mission.description }}</td>
                <td>{{ mission.dateDebut ? mission.dateDebut|date('Y-m-d') : '' }}</td>
                <td>{{ mission.dateFin ? mission.dateFin|date('Y-m-d') : '' }}</td>
                <td>{{ mission.nom }}</td>
                <td>
                    {% if mission.competences is not empty %}
                        <ul>
                            {% for comp in mission.competences %}
                                <li>{{ comp.nom }}</li>
                            {% endfor %}
                        </ul>
                    {% else %}
                        <em>Aucune compétence</em>
                    {% endif %}
                </td>
                <td>
                    <a href=\"{{ path('app_mission_show', {'id': mission.id}) }}\">show</a>
                    <a href=\"{{ path('app_mission_edit', {'id': mission.id}) }}\">edit</a>
                </td>
            </tr>
        {% else %}
            <tr>
                <td colspan=\"8\">no records found</td>
            </tr>
        {% endfor %}
        </tbody>
    </table>
    

    <a href=\"{{ path('app_mission_new') }}\">Create new</a>
{% endblock %}
", "mission/index.html.twig", "C:\\Users\\rescue123\\Desktop\\Taskforce\\TaskForce-backend\\templates\\mission\\index.html.twig");
    }
}
