<?php

declare(strict_types = 1);

namespace App\Tests\Orm\ContentManagement;

use App\Doctrine\Entity\Content;
use App\Doctrine\Entity\Language;
use App\Doctrine\Repository\ContentRepository;
use App\Orm\ContentManagement\ContentProvider;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class ContentProviderTest extends TestCase
{
    public function testFindByHashes_ReturnsAppropriateContents() : void
    {
        $provider = $this->createContentProvider();

        $contents = $provider->findByHashes([
            '85fb734b-94af-4f97-a98c-741189117fb6',
            '7fd5066c-d8cc-4159-9fed-98392db1f0cb',
            'cab045c2-d6f6-4464-929a-f8b444df16e8',
        ]);

        /** @var Content $content */
        $content = $contents->getInfo();
        $this->assertEquals(['text' => 'Hello World'], $content->getContent());
        $contents->next();

        $content = $contents->getInfo();
        $this->assertEquals(['text' => 'Кофе хочеш?'], $content->getContent());
        $contents->next();

        $content = $contents->getInfo();
        $this->assertEquals(['text' => 'Keep It Simple'], $content->getContent());
    }

    protected function createContentProvider() : ContentProvider
    {
        $language = new Language();
        $language->setId(1);
        $language->setIsDefault(false);
        $language->setCode('en');
        $language->setName('English');

        $repo = $this->createStub(ContentRepository::class);
        $repo
            ->method('findByHashesAndLanguage')
            ->willReturn(new ArrayCollection([
                $this->createContent([
                    'hash'       => '85fb734b-94af-4f97-a98c-741189117fb6',
                    'languageId' => 1,
                    'content'    => ['text' => 'Hello World']
                ]),
                $this->createContent([
                    'hash'       => '85fb734b-94af-4f97-a98c-741189117fb6',
                    'languageId' => null,
                    'content'    => ['text' => 'Привет Мир']
                ]),
                $this->createContent([
                    'hash'       => '7fd5066c-d8cc-4159-9fed-98392db1f0cb',
                    'languageId' => null,
                    'content'    => ['text' => 'Кофе хочеш?']
                ]),
                $this->createContent([
                    'hash'       => 'cab045c2-d6f6-4464-929a-f8b444df16e8',
                    'languageId' => 1,
                    'content'    => ['text' => 'Keep It Simple']
                ]),
                $this->createContent([
                    'hash'       => 'cab045c2-d6f6-4464-929a-f8b444df16e8',
                    'languageId' => null,
                    'content'    => ['text' => 'Делай проще, тупица']
                ]),
            ]));

        return new ContentProvider($repo, $language);
    }

    protected function createContent(array $data) : Content
    {
        $content = new Content();
        $content->setHash($data['hash']);
        $content->setLanguageId($data['languageId']);
        $content->setContent($data['content']);

        return $content;
    }
}
