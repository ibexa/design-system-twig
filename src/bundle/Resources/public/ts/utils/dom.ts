export const createNodesFromTemplate = (template: string, placeholders: Record<string, string>): NodeListOf<ChildNode> | null => {
    const container = document.createElement('div');
    let result = template;

    Object.entries(placeholders).forEach(([placeholder, value]) => {
        result = result.replaceAll(placeholder, value);
    });

    container.innerHTML = result;

    if (container instanceof HTMLElement && container.childNodes.length > 0) {
        return container.childNodes;
    }

    return null;
};
