import { escapeHTML } from '@ids-core/helpers/escape';

export const escapeTemplateValue = (value: string): string =>
    escapeHTML(value).replace(/["']/g, (quote) => (quote === '"' ? '&quot;' : '&#39;'));

export const createNodesFromTemplate = (template: string, placeholders: Record<string, string>): NodeListOf<ChildNode> | null => {
    const container = document.createElement('div');
    let result = template;

    Object.entries(placeholders).forEach(([placeholder, value]) => {
        const escapedValue = escapeTemplateValue(value);

        result = result.replaceAll(placeholder, () => escapedValue);
    });

    container.innerHTML = result;

    if (container instanceof HTMLElement && container.childNodes.length > 0) {
        return container.childNodes;
    }

    return null;
};
