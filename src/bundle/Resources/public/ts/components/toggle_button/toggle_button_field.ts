import { Base } from '../../partials';
import { HelperText } from '../helper_text';
import { Label } from '../label';
import { ToggleButtonInput } from './toggle_button_input';

export class ToggleButtonField extends Base {
    private helperTextInstance: HelperText | null = null;
    private inputInstance: ToggleButtonInput;
    private labelInstance: Label | null = null;

    constructor(container: HTMLDivElement) {
        super(container);

        const inputContainer = container.querySelector<HTMLDivElement>('.ids-toggle');

        if (!inputContainer) {
            throw new Error('ToggleButtonField: Input container is missing in the container.');
        }

        const labelContainer = container.querySelector<HTMLDivElement>('.ids-label');

        if (labelContainer) {
            this.labelInstance = new Label(labelContainer);
        }

        const helperTextContainer = container.querySelector<HTMLDivElement>('.ids-helper-text');

        if (helperTextContainer) {
            this.helperTextInstance = new HelperText(helperTextContainer);
        }

        this.inputInstance = new ToggleButtonInput(inputContainer);
    }

    initChildren(): void {
        this.labelInstance?.init();
        this.inputInstance.init();
        this.helperTextInstance?.init();
    }

    init(): void {
        super.init();

        this.initChildren();
    }
}
