const registry = new Map();

export function registerBlock(type, Component) {
  if (typeof type !== 'string' || !type) return;
  registry.set(type, Component);
}

export function getBlockComponent(type) {
  return registry.get(type);
}

export function getRegistry() {
  return registry;
}
