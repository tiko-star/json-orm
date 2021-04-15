<?php

declare(strict_types = 1);

namespace App\Tests\Orm\ContentManagement;

use App\Doctrine\Entity\Content;
use App\Orm\ContentManagement\ContentDispatcher;
use App\Orm\ContentManagement\DispatchedContent;
use App\Orm\Entity\Hash;
use App\Orm\Persistence\ContentObjectStorage;
use PHPUnit\Framework\TestCase;
use SplObjectStorage;

class ContentDispatcherTest extends TestCase
{
    /**
     * @dataProvider contentProvider
     *
     * @param \App\Orm\Persistence\ContentObjectStorage $upcoming
     * @param \App\Orm\Persistence\ContentObjectStorage $existing
     */
    public function testDispatch_ReturnsNewContent(ContentObjectStorage $upcoming, ContentObjectStorage $existing) : void
    {
        $dispatcher = $this->createContentDispatcher();
        $dispatched = $dispatcher->dispatch($upcoming, $existing);

        $this->assertInstanceOf(DispatchedContent::class, $dispatched);
        $this->assertInstanceOf(SplObjectStorage::class, $dispatched->getNew());

        $new = $dispatched->getNew();

        $this->assertCount(2, $new);

        /** @var Content $content */
        $content = $new->getInfo();
        $this->assertEquals(['text' => 'Click Here!'], $content->getContent());
        // Rewind iterator.
        $new->next();
        /** @var Content $content */
        $content = $new->getInfo();
        $this->assertEquals(['text' => 'Follow Me!'], $content->getContent());
    }

    /**
     * @dataProvider contentProvider
     *
     * @param \App\Orm\Persistence\ContentObjectStorage $upcoming
     * @param \App\Orm\Persistence\ContentObjectStorage $existing
     */
    public function testDispatch_ReturnsModifiedContent(ContentObjectStorage $upcoming, ContentObjectStorage $existing) : void
    {
        $dispatcher = $this->createContentDispatcher();
        $dispatched = $dispatcher->dispatch($upcoming, $existing);

        $this->assertInstanceOf(DispatchedContent::class, $dispatched);
        $this->assertInstanceOf(SplObjectStorage::class, $dispatched->getModified());

        $modified = $dispatched->getModified();

        $this->assertCount(2, $modified);

        /** @var Content $content */
        $content = $modified->getInfo();
        $this->assertEquals(['text' => 'Welcome to the Jungle'], $content->getContent());
        $this->assertEquals('71e66622-71dd-4c99-97fc-065389769f2b', (string) $modified->current());
        // Rewind iterator.
        $modified->next();
        /** @var Content $content */
        $content = $modified->getInfo();
        $this->assertEquals(['text' => 'Dreams are real!!!'], $content->getContent());
        $this->assertEquals('bae4a9fb-f8c8-4ec1-a84d-e294ec538b12', (string) $modified->current());
    }

    /**
     * @dataProvider contentProvider
     *
     * @param \App\Orm\Persistence\ContentObjectStorage $upcoming
     * @param \App\Orm\Persistence\ContentObjectStorage $existing
     */
    public function testDispatch_ReturnsRemovedContent(ContentObjectStorage $upcoming, ContentObjectStorage $existing) : void
    {
        $dispatcher = $this->createContentDispatcher();
        $dispatched = $dispatcher->dispatch($upcoming, $existing);

        $this->assertInstanceOf(DispatchedContent::class, $dispatched);
        $this->assertInstanceOf(SplObjectStorage::class, $dispatched->getRemoved());

        $removed = $dispatched->getRemoved();

        $this->assertCount(3, $removed);

        /** @var Content $content */
        $content = $removed->getInfo();
        $this->assertEquals(['text' => 'Avocado Kiwi Strawberry'], $content->getContent());
        $this->assertEquals('dee5fa8a-f9c4-4a9e-af71-5170486f2ef9', (string) $removed->current());
        // Rewind iterator.
        $removed->next();

        /** @var Content $content */
        $content = $removed->getInfo();
        $this->assertEquals(['text' => 'Banana Orange Apple'], $content->getContent());
        $this->assertEquals('3520f841-08a1-4bc8-a086-b4ef312c63ba', (string) $removed->current());
        // Rewind iterator.
        $removed->next();

        /** @var Content $content */
        $content = $removed->getInfo();
        $this->assertEquals(['text' => 'Define your goals!!!'], $content->getContent());
        $this->assertEquals('c73486d9-9f14-4244-8dae-bcef7e78e4ac', (string) $removed->current());
    }

    protected function createContentDispatcher() : ContentDispatcher
    {
        return new ContentDispatcher();
    }

    public function contentProvider() : array
    {
        $upcoming = $this->createUpcomingContent();
        $existing = $this->createExistingContent();

        return [
            [
                $upcoming,
                $existing
            ]
        ];
    }

    protected function createUpcomingContent() : SplObjectStorage
    {
        $upcoming = new ContentObjectStorage();

        // Attach new content.
        $content1 = new Content();
        $content1->setHash('093d01d0-f458-418e-87df-936def099ab4');
        $content1->setContent(['text' => 'Click Here!']);

        $content2 = new Content();
        $content2->setHash('7d2a5026-4da4-4583-bdf8-66a57ec936f7');
        $content2->setContent(['text' => 'Follow Me!']);

        $upcoming->attach(new Hash('__093d01d0-f458-418e-87df-936def099ab4'), $content1);
        $upcoming->attach(new Hash('__7d2a5026-4da4-4583-bdf8-66a57ec936f7'), $content2);

        // Attach modified content.
        $content3 = new Content();
        $content3->setHash('71e66622-71dd-4c99-97fc-065389769f2b');
        $content3->setContent(['text' => 'Welcome to the Jungle']);

        $content4 = new Content();
        $content4->setHash('bae4a9fb-f8c8-4ec1-a84d-e294ec538b12');
        $content4->setContent(['text' => 'Dreams are real!!!']);

        $upcoming->attach(new Hash('71e66622-71dd-4c99-97fc-065389769f2b'), $content3);
        $upcoming->attach(new Hash('bae4a9fb-f8c8-4ec1-a84d-e294ec538b12'), $content4);

        // Attach unmodified content.
        $content5 = new Content();
        $content5->setHash('b4dd37ac-7df2-44c1-b97d-0f0062846796');
        $content5->setContent(['text' => 'Click Here!']);

        $content6 = new Content();
        $content6->setHash('eff06715-8d5f-4c98-b614-4bb375e32375');
        $content6->setContent(['text' => 'Follow Me!']);

        $upcoming->attach(new Hash('b4dd37ac-7df2-44c1-b97d-0f0062846796'), $content5);
        $upcoming->attach(new Hash('eff06715-8d5f-4c98-b614-4bb375e32375'), $content6);

        // Attach content which is empty and is going to be removed.
        $content7 = new Content();
        $content7->setHash('c73486d9-9f14-4244-8dae-bcef7e78e4ac');
        $content7->setContent([]);

        $upcoming->attach(new Hash('c73486d9-9f14-4244-8dae-bcef7e78e4ac'), $content7);

        return $upcoming;
    }

    protected function createExistingContent() : SplObjectStorage
    {
        $existing = new ContentObjectStorage();

        // Attach unmodified content.
        $content1 = new Content();
        $content1->setHash('b4dd37ac-7df2-44c1-b97d-0f0062846796');
        $content1->setContent(['text' => 'Click Here!']);

        $content2 = new Content();
        $content2->setHash('eff06715-8d5f-4c98-b614-4bb375e32375');
        $content2->setContent(['text' => 'Follow Me!']);

        $existing->attach(new Hash('b4dd37ac-7df2-44c1-b97d-0f0062846796'), $content1);
        $existing->attach(new Hash('eff06715-8d5f-4c98-b614-4bb375e32375'), $content2);

        // Attach content that is going to be modified.
        $content3 = new Content();
        $content3->setHash('71e66622-71dd-4c99-97fc-065389769f2b');
        $content3->setContent(['text' => 'Welcome to the Club']);

        $content4 = new Content();
        $content4->setHash('bae4a9fb-f8c8-4ec1-a84d-e294ec538b12');
        $content4->setContent(['text' => 'Dreams come true!!!']);

        $existing->attach(new Hash('71e66622-71dd-4c99-97fc-065389769f2b'), $content3);
        $existing->attach(new Hash('bae4a9fb-f8c8-4ec1-a84d-e294ec538b12'), $content4);

        // Attach content that is going to be removed.
        $content5 = new Content();
        $content5->setHash('dee5fa8a-f9c4-4a9e-af71-5170486f2ef9');
        $content5->setContent(['text' => 'Avocado Kiwi Strawberry']);

        $content6 = new Content();
        $content6->setHash('3520f841-08a1-4bc8-a086-b4ef312c63ba');
        $content6->setContent(['text' => 'Banana Orange Apple']);

        $existing->attach(new Hash('dee5fa8a-f9c4-4a9e-af71-5170486f2ef9'), $content5);
        $existing->attach(new Hash('3520f841-08a1-4bc8-a086-b4ef312c63ba'), $content6);

        // Attach content which is going to be empty and be removed later on.
        $content7 = new Content();
        $content7->setHash('c73486d9-9f14-4244-8dae-bcef7e78e4ac');
        $content7->setContent(['text' => 'Define your goals!!!']);

        $existing->attach(new Hash('c73486d9-9f14-4244-8dae-bcef7e78e4ac'), $content7);

        return $existing;
    }
}
