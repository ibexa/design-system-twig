import { Base } from '../partials';

export default class Chip extends Base {
    private deleteButton: HTMLButtonElement | null;

    constructor(container: HTMLDivElement) {
        super(container);

        this.deleteButton = this._container.querySelector('.ids-chip__delete');
    }

    protected handleDelete(event: MouseEvent): void {
        event.stopPropagation();

        this.delete();
    }

    public delete(): void {
        const deleteEvent = new CustomEvent('ids:chip:delete:before', {
            cancelable: true,
            detail: {
                component: this,
            },
        });

        this._container.dispatchEvent(deleteEvent);

        if (deleteEvent.defaultPrevented) {
            return;
        }

        this._container.remove();
    }

    protected initDeleteButton(): void {
        if (this.deleteButton) {
            this.deleteButton.addEventListener('click', this.handleDelete.bind(this));
        }
    }

    public init(): void {
        super.init();
        this.initDeleteButton();
    }
}
