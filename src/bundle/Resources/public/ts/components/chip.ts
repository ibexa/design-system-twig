import { Base } from '../partials';

interface ChipConfig {
    onDelete?: (event: Event) => void;
}

export default class Chip extends Base {
    private deleteButton: HTMLButtonElement | null;
    private onDelete?: (event: Event) => void;

    constructor(container: HTMLDivElement, config: ChipConfig = {}) {
        super(container);

        this.deleteButton = this._container.querySelector('.ids-chip__delete');
        this.onDelete = config.onDelete;
    }

    private handleDelete(event: MouseEvent): void {
        event.stopPropagation();
        if (this.onDelete) {
            this.onDelete(event);
        }
    }

    private initDeleteButton(deleteButton: HTMLButtonElement): void {
        deleteButton.addEventListener('click', this.handleDelete.bind(this));
    }

    public init(): void {
        if (this.deleteButton) {
            this.initDeleteButton(this.deleteButton);
        }
        super.init();
    }
}
