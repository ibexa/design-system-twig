import { Base } from '../partials';

export default class Chip extends Base {
    private closeButton: HTMLButtonElement | null;

    constructor(container: HTMLDivElement) {
        super(container);

        this.closeButton = this._container.querySelector('.ids-chip-clear-btn');
        this.init();
    }

    public init(): void {
        if (this.closeButton) {
            this.closeButton.addEventListener('click', this.handleClose.bind(this));
        }
    }

    private handleClose(event: Event): void {
        event.stopPropagation();
        this._container.remove();
    }
}
