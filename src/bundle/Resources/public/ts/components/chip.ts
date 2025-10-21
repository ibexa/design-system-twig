import { Base } from '../partials';

export default class Chip extends Base {
    private closeButton: HTMLButtonElement | null;
    private onClose: (event: Event) => void;

    constructor(container: HTMLDivElement, onClose: (event: Event) => void) {
        super(container);

        this.closeButton = this._container.querySelector('.ids-chip__close');
        this.onClose = onClose;
        this.init();
    }

    public init(): void {
        if (this.closeButton) {
            this.closeButton.addEventListener('click', this.handleClose.bind(this));
        }

        super.init();
    }

    private handleClose(event: Event): void {
        event.stopPropagation();
        this.onClose(event);
    }
}
