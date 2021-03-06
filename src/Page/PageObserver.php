<?php namespace Anomaly\PagesModule\Page;

use Anomaly\PagesModule\Page\Command\DeleteChildren;
use Anomaly\PagesModule\Page\Command\GenerateRoutesFile;
use Anomaly\PagesModule\Page\Contract\PageInterface;
use Anomaly\Streams\Platform\Entry\Contract\EntryInterface;
use Anomaly\Streams\Platform\Entry\EntryModel;
use Anomaly\Streams\Platform\Entry\EntryObserver;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class PageObserver
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\PagesModule\Page
 */
class PageObserver extends EntryObserver
{

    /**
     * Fired just before saving the page.
     *
     * @param EntryInterface|PageInterface|EntryModel $entry
     */
    public function saving(EntryInterface $entry)
    {
        /* @var Builder $query */
        if ($entry->isHome() && $query = $entry->newQuery()) {
            $query->update(['home' => false]);
        }

        if (!$entry->getStrId()) {
            $entry->str_id = str_random();
        }

        parent::saving($entry);
    }

    /**
     * Fired after a page is created.
     *
     * @param EntryInterface|PageInterface $entry
     */
    public function created(EntryInterface $entry)
    {
        $this->commands->dispatch(new GenerateRoutesFile());

        parent::created($entry);
    }

    /**
     * Fired after a page is updated.
     *
     * @param EntryInterface|PageInterface $entry
     */
    public function updated(EntryInterface $entry)
    {
        $this->commands->dispatch(new GenerateRoutesFile());

        parent::updated($entry);
    }

    /**
     * Fired after a page is deleted.
     *
     * @param EntryInterface|PageInterface $entry
     */
    public function deleted(EntryInterface $entry)
    {
        $this->commands->dispatch(new DeleteChildren($entry));
        $this->commands->dispatch(new GenerateRoutesFile());

        parent::deleted($entry);
    }
}
