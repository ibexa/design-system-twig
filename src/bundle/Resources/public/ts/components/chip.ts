import { Base } from '../partials';

interface ChipConfig {
    onDelete?: (event: MouseEvent) => void;
}

export class Chip extends Base {
    private deleteButton: HTMLButtonElement | null;
    private onDelete?: (event: MouseEvent) => void;

    constructor(container: HTMLDivElement, config: ChipConfig = {}) {
        super(container);

        this.deleteButton = this._container.querySelector('.ids-chip__delete');
        this.onDelete = config.onDelete;
    }

    protected handleDelete(event: MouseEvent): void {
        event.stopPropagation();

        if (this.onDelete) {
            this.onDelete(event);
        }
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
