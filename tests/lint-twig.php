<?php

declare(strict_types=1);

/*
 * Syntax-Check aller Twig-Templates des Bundles (ohne Contao-Installation).
 *
 * Contao-spezifische Funktionen, Filter und Tags werden durch Platzhalter
 * ersetzt; geprüft wird nur, ob sich jedes Template fehlerfrei parsen lässt.
 *
 * Aufruf: php tests/lint-twig.php [pfad/zu/autoload.php]
 */

use Twig\Environment;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;
use Twig\Node\Node;
use Twig\Source;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;
use Twig\TwigFilter;
use Twig\TwigFunction;

require $argv[1] ?? __DIR__.'/../vendor/autoload.php';

$templateDir = dirname(__DIR__).'/contao/templates';

$loader = new FilesystemLoader();
$loader->addPath($templateDir, 'Contao');

$twig = new Environment($loader);
$twig->registerUndefinedFunctionCallback(static fn (string $name) => new TwigFunction($name, static fn (...$args) => null, ['is_variadic' => true]));
$twig->registerUndefinedFilterCallback(static fn (string $name) => new TwigFilter($name, static fn ($value, ...$args) => $value, ['is_variadic' => true]));

// {% trans_default_domain 'contao_default' %} (Symfony Translation)
$twig->addTokenParser(new class extends AbstractTokenParser {
    public function parse(Token $token): Node
    {
        $this->parser->getExpressionParser()->parseExpression();
        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);

        return new Node([], [], $token->getLine());
    }

    public function getTag(): string
    {
        return 'trans_default_domain';
    }
});

// Contao-Block-Tags {% slot %}…{% endslot %} und {% defer %}…{% enddefer %}
foreach (['slot' => true, 'defer' => false] as $tag => $hasName) {
    $twig->addTokenParser(new class($tag, $hasName) extends AbstractTokenParser {
        public function __construct(private readonly string $tag, private readonly bool $hasName)
        {
        }

        public function parse(Token $token): Node
        {
            $stream = $this->parser->getStream();

            if ($this->hasName) {
                $stream->expect(Token::NAME_TYPE);
            }

            $stream->expect(Token::BLOCK_END_TYPE);
            $end = 'end'.$this->tag;

            do {
                $this->parser->subparse(static fn (Token $t) => $t->test(['else', $end]));
                $next = $stream->next()->getValue();
                $stream->expect(Token::BLOCK_END_TYPE);
            } while ($next === 'else');

            return new Node([], [], $token->getLine());
        }

        public function getTag(): string
        {
            return $this->tag;
        }
    });
}

$errors = 0;
$count = 0;

$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($templateDir, FilesystemIterator::SKIP_DOTS));

foreach ($files as $file) {
    if (!str_ends_with($file->getFilename(), '.twig')) {
        continue;
    }

    ++$count;
    $name = '@Contao/'.substr($file->getPathname(), strlen($templateDir) + 1);

    try {
        $twig->parse($twig->tokenize(new Source(file_get_contents($file->getPathname()), $name, $file->getPathname())));
    } catch (SyntaxError $e) {
        ++$errors;
        fwrite(STDERR, sprintf("FEHLER %s: %s\n", $name, $e->getMessage()));
    }
}

printf("%d Templates geprüft, %d Fehler\n", $count, $errors);

exit($errors > 0 ? 1 : 0);
