document.addEventListener('click', event => {
  document.querySelectorAll('.header-menu[open]').forEach(menu => {
    if (!menu.contains(event.target)) {
      menu.removeAttribute('open');
    }
  });
});

document.querySelectorAll('[data-dual-range]').forEach(range => {
  const minInput = range.querySelector('input[name="min_price"]');
  const maxInput = range.querySelector('input[name="max_price"]');
  const minLabel = range.querySelector('[data-min-label]');
  const maxLabel = range.querySelector('[data-max-label]');
  const minValue = Number(minInput.min || 0);
  const maxValue = Number(maxInput.max || 100);

  const sync = changed => {
    let min = Number(minInput.value);
    let max = Number(maxInput.value);

    if (min > max) {
      if (changed === minInput) {
        max = min;
        maxInput.value = max;
      } else {
        min = max;
        minInput.value = min;
      }
    }

    minLabel.textContent = min.toLocaleString();
    maxLabel.textContent = max.toLocaleString();
    range.style.setProperty('--range-min', `${((min - minValue) / (maxValue - minValue)) * 100}%`);
    range.style.setProperty('--range-max', `${((max - minValue) / (maxValue - minValue)) * 100}%`);
  };

  minInput.addEventListener('input', () => sync(minInput));
  maxInput.addEventListener('input', () => sync(maxInput));
  sync();
});

document.querySelectorAll('[data-confirm-open]').forEach(button => {
  button.addEventListener('click', () => {
    const modal = document.getElementById(button.dataset.confirmOpen);
    if (modal) modal.hidden = false;
  });
});

document.addEventListener('click', event => {
  if (event.target.matches('[data-confirm-close]')) {
    event.target.closest('.modal-backdrop').hidden = true;
  }
});

document.querySelectorAll('[data-gallery]').forEach(gallery => {
  const main = gallery.querySelector('[data-gallery-main]');
  const thumbs = [...gallery.querySelectorAll('[data-gallery-thumb]')];
  const prev = gallery.querySelector('[data-gallery-prev]');
  const next = gallery.querySelector('[data-gallery-next]');
  let currentIndex = 0;

  const show = index => {
    if (!main || !thumbs.length) return;
    currentIndex = (index + thumbs.length) % thumbs.length;
    const image = thumbs[currentIndex].querySelector('img');
    main.src = image.src;
    main.alt = image.alt;
    thumbs.forEach((thumb, thumbIndex) => {
      thumb.classList.toggle('is-active', thumbIndex === currentIndex);
    });
  };

  thumbs.forEach((thumb, index) => {
    thumb.addEventListener('click', () => show(index));
  });

  if (prev) prev.addEventListener('click', () => show(currentIndex - 1));
  if (next) next.addEventListener('click', () => show(currentIndex + 1));
});

document.querySelectorAll('[data-collapsible-list]').forEach(list => {
  const items = [...list.querySelectorAll('[data-collapsible-item]')];
  const button = list.querySelector('[data-collapsible-toggle]');
  const collapsedCount = Number(list.dataset.collapsedCount || items.length);
  let expanded = false;

  const sync = () => {
    items.forEach((item, index) => {
      item.hidden = !expanded && index >= collapsedCount;
    });

    if (button) {
      button.hidden = items.length <= collapsedCount;
      button.textContent = expanded ? '閉じる' : 'もっと表示する';
    }
  };

  if (button) {
    button.addEventListener('click', () => {
      expanded = !expanded;
      sync();
    });
  }

  sync();
});

document.querySelectorAll('[data-subgenre-select]').forEach(select => {
  const parent = document.getElementById(select.dataset.parentSelect);
  if (!parent) return;

  const options = [...select.options];
  const sync = () => {
    const genreId = parent.value;
    let selectedAvailable = false;
    let firstAvailable = null;

    options.forEach(option => {
      const isBlank = option.value === '';
      const available = isBlank || !genreId || option.dataset.genreId === genreId;
      option.hidden = !available;
      option.disabled = !available;

      if (available && !isBlank && !firstAvailable) {
        firstAvailable = option;
      }

      if (available && option.selected) {
        selectedAvailable = true;
      }
    });

    if (!selectedAvailable) {
      const blank = options.find(option => option.value === '' && !option.disabled);
      select.value = blank ? '' : firstAvailable ? firstAvailable.value : '';
    }
  };

  parent.addEventListener('change', sync);
  sync();
});

document.querySelectorAll('[data-favorite-form]').forEach(form => {
  form.addEventListener('submit', () => {
    const button = form.querySelector('[data-favorite-button]');
    if (!button) return;

    const count = button.querySelector('[data-favorite-count]');
    const currentCount = Number((count?.textContent || '0').replace(/,/g, '')) || 0;
    const favorited = !button.classList.contains('is-active');
    updateFavoriteButtons(button.dataset.contentId, {
      favorited,
      count: Math.max(0, currentCount + (favorited ? 1 : -1)),
    });
  });
});

function updateFavoriteButtons(contentId, data) {
  document.querySelectorAll(`[data-favorite-button][data-content-id="${contentId}"]`).forEach(button => {
    const icon = button.querySelector('[data-favorite-icon]');
    const count = button.querySelector('[data-favorite-count]');
    button.classList.toggle('is-active', data.favorited);

    if (icon) {
      icon.src = data.favorited ? button.dataset.activeIcon : button.dataset.inactiveIcon;
    }

    if (count) {
      count.textContent = Number(data.count || 0).toLocaleString('ja-JP');
    }
  });
}

const imageQueues = new WeakMap();

document.querySelectorAll('[data-image-cropper]').forEach(wrapper => {
  const input = wrapper.querySelector('[data-file-input]');
  const preview = wrapper.querySelector('[data-preview]');

  input.addEventListener('change', async () => {
    const file = input.files[0];
    if (!file) return;

    const cropped = await openImageCropper(file, {
      aspect: fixedAspect(wrapper.dataset.aspect),
      maxWidth: Number(wrapper.dataset.maxWidth || 720),
      maxHeight: Number(wrapper.dataset.maxHeight || 720),
    });

    if (!cropped) {
      input.value = '';
      return;
    }

    const transfer = new DataTransfer();
    transfer.items.add(cropped);
    input.files = transfer.files;
    preview.src = URL.createObjectURL(cropped);
  });
});

document.querySelectorAll('[data-image-repeater]').forEach(wrapper => {
  const input = wrapper.querySelector('[data-file-input]');
  const list = wrapper.querySelector('[data-image-list]');
  const maxImages = Number(wrapper.dataset.maxImages || 20);
  imageQueues.set(input, []);

  input.addEventListener('change', async () => {
    const files = [...input.files];
    const queue = imageQueues.get(input);

    for (const file of files) {
      if (list.querySelectorAll('.image-record').length >= maxImages) {
        alert(`コンテンツ画像は最大${maxImages}枚まで追加できます。`);
        break;
      }

      const cropped = await openImageCropper(file, {
        aspect: fixedAspect(wrapper.dataset.aspect),
        maxWidth: 1600,
        maxHeight: 1600,
      });

      if (!cropped) continue;

      queue.push({
        file: cropped,
        url: URL.createObjectURL(cropped),
      });

      renderImageQueue(input, list);
    }

    syncImageInput(input);
  });
});

document.addEventListener('click', event => {
  const newImageButton = event.target.closest('[data-remove-new-image]');

  if (newImageButton) {
    const wrapper = newImageButton.closest('[data-image-repeater]');
    const input = wrapper.querySelector('[data-file-input]');
    const list = wrapper.querySelector('[data-image-list]');
    const queue = imageQueues.get(input);
    const removed = queue.splice(Number(newImageButton.dataset.removeNewImage), 1)[0];

    if (removed) {
      URL.revokeObjectURL(removed.url);
    }

    renderImageQueue(input, list);
    syncImageInput(input);
    return;
  }

  const button = event.target.closest('[data-delete-image]');
  if (!button) return;

  const form = button.closest('form');
  const record = button.closest('[data-image-record]');
  const input = document.createElement('input');
  input.type = 'hidden';
  input.name = 'delete_image_ids[]';
  input.value = button.dataset.deleteImage;
  form.appendChild(input);
  if (record) record.remove();
});

function syncImageInput(input) {
  const transfer = new DataTransfer();
  imageQueues.get(input).forEach(item => transfer.items.add(item.file));
  input.files = transfer.files;
}

function renderImageQueue(input, list) {
  list.querySelectorAll('[data-new-image]').forEach(record => record.remove());

  const persistedCount = list.querySelectorAll('.image-record').length;
  imageQueues.get(input).forEach((item, index) => {
    const record = document.createElement('div');
    const image = document.createElement('img');
    const label = document.createElement('span');
    const button = document.createElement('button');
    const icon = document.createElement('span');

    record.className = 'image-record';
    record.dataset.imageRecord = '';
    record.dataset.newImage = '';
    image.src = item.url;
    image.alt = '';
    label.textContent = `追加画像 ${persistedCount + index + 1}`;
    button.className = 'image-record__remove';
    button.type = 'button';
    button.dataset.removeNewImage = String(index);
    button.setAttribute('aria-label', '画像を削除');
    icon.className = 'material-symbols-outlined';
    icon.setAttribute('aria-hidden', 'true');
    icon.textContent = 'close';

    button.appendChild(icon);
    record.append(image, label, button);
    list.appendChild(record);
  });
}

function fixedAspect(value) {
  const aspect = Number(value);

  return Number.isFinite(aspect) && aspect > 0 ? aspect : null;
}

function openImageCropper(file, options) {
  return new Promise(resolve => {
    const url = URL.createObjectURL(file);
    const aspect = options.aspect;
    const modal = document.createElement('div');
    modal.className = 'modal-backdrop crop-modal';
    modal.innerHTML = `
      <div class="modal-card modal-card--crop" role="dialog" aria-modal="true" aria-label="画像クロップ">
        <div class="crop-stage">
          <img src="${url}" alt="">
          <div class="crop-box" tabindex="0">
            <span class="crop-box__handle crop-box__handle--nw" data-crop-handle="nw"></span>
            <span class="crop-box__handle crop-box__handle--n" data-crop-handle="n"></span>
            <span class="crop-box__handle crop-box__handle--ne" data-crop-handle="ne"></span>
            <span class="crop-box__handle crop-box__handle--e" data-crop-handle="e"></span>
            <span class="crop-box__handle crop-box__handle--se" data-crop-handle="se"></span>
            <span class="crop-box__handle crop-box__handle--s" data-crop-handle="s"></span>
            <span class="crop-box__handle crop-box__handle--sw" data-crop-handle="sw"></span>
            <span class="crop-box__handle crop-box__handle--w" data-crop-handle="w"></span>
          </div>
        </div>
        <div class="form-actions crop-actions">
          <button class="button button--primary" type="button" data-crop-apply>クロップ</button>
          <button class="button button--ghost" type="button" data-crop-cancel>戻る</button>
        </div>
      </div>
    `;
    document.body.appendChild(modal);

    const img = modal.querySelector('img');
    const box = modal.querySelector('.crop-box');
    const minSize = 64;
    let action = null;

    const cleanup = value => {
      URL.revokeObjectURL(url);
      modal.remove();
      resolve(value);
    };

    const readBox = () => ({
      left: parseFloat(box.style.left || 0),
      top: parseFloat(box.style.top || 0),
      width: parseFloat(box.style.width || box.offsetWidth),
      height: parseFloat(box.style.height || box.offsetHeight),
    });

    const imageSize = () => ({
      width: img.getBoundingClientRect().width,
      height: img.getBoundingClientRect().height,
    });

    const normalizeBox = next => {
      const size = imageSize();
      let width = Math.max(minSize, next.width);
      let height = Math.max(minSize, next.height);

      if (aspect) {
        width = Math.min(width, size.width);
        height = width / aspect;

        if (height > size.height) {
          height = size.height;
          width = height * aspect;
        }
      } else {
        width = Math.min(width, size.width);
        height = Math.min(height, size.height);
      }

      return {
        left: Math.max(0, Math.min(next.left, size.width - width)),
        top: Math.max(0, Math.min(next.top, size.height - height)),
        width,
        height,
      };
    };

    const applyBox = next => {
      const normalized = normalizeBox(next);
      box.style.left = `${normalized.left}px`;
      box.style.top = `${normalized.top}px`;
      box.style.width = `${normalized.width}px`;
      box.style.height = `${normalized.height}px`;
    };

    const boxAfterMove = event => {
      const start = action.startBox;
      return {
        left: start.left + event.clientX - action.startX,
        top: start.top + event.clientY - action.startY,
        width: start.width,
        height: start.height,
      };
    };

    const boxAfterResize = event => {
      const handle = action.handle;
      const dx = event.clientX - action.startX;
      const dy = event.clientY - action.startY;
      const start = action.startBox;

      if (aspect) {
        const anchorRight = start.left + start.width;
        const anchorBottom = start.top + start.height;
        const verticalOnly = !handle.includes('e') && !handle.includes('w');
        const nextHeight = handle.includes('n') ? start.height - dy : start.height + dy;
        let width = verticalOnly
          ? nextHeight * aspect
          : handle.includes('w')
            ? start.width - dx
            : start.width + dx;
        width = Math.max(minSize, width);
        const height = width / aspect;

        return {
          left: handle.includes('w') ? anchorRight - width : start.left,
          top: handle.includes('n') ? anchorBottom - height : start.top,
          width,
          height,
        };
      }

      let left = start.left;
      let top = start.top;
      let width = start.width;
      let height = start.height;

      if (handle.includes('w')) {
        left += dx;
        width -= dx;
      }

      if (handle.includes('e')) width += dx;

      if (handle.includes('n')) {
        top += dy;
        height -= dy;
      }

      if (handle.includes('s')) height += dy;

      if (width < minSize) {
        if (handle.includes('w')) left -= minSize - width;
        width = minSize;
      }

      if (height < minSize) {
        if (handle.includes('n')) top -= minSize - height;
        height = minSize;
      }

      return { left, top, width, height };
    };

    img.addEventListener('load', () => {
      const size = imageSize();

      if (aspect) {
        let width = size.width * 0.72;
        let height = width / aspect;

        if (height > size.height * 0.72) {
          height = size.height * 0.72;
          width = height * aspect;
        }

        applyBox({
          left: (size.width - width) / 2,
          top: (size.height - height) / 2,
          width,
          height,
        });
      } else {
        applyBox({
          left: size.width * 0.14,
          top: size.height * 0.14,
          width: size.width * 0.72,
          height: size.height * 0.72,
        });
      }
    });

    box.addEventListener('pointerdown', event => {
      const handle = event.target.closest('[data-crop-handle]');
      action = {
        type: handle ? 'resize' : 'move',
        handle: handle ? handle.dataset.cropHandle : null,
        startX: event.clientX,
        startY: event.clientY,
        startBox: readBox(),
      };
      event.preventDefault();
      box.setPointerCapture(event.pointerId);
    });

    box.addEventListener('pointermove', event => {
      if (!action) return;

      applyBox(action.type === 'resize' ? boxAfterResize(event) : boxAfterMove(event));
    });

    const stopAction = event => {
      action = null;
      if (box.hasPointerCapture(event.pointerId)) {
        box.releasePointerCapture(event.pointerId);
      }
    };

    box.addEventListener('pointerup', stopAction);
    box.addEventListener('pointercancel', stopAction);

    modal.querySelector('[data-crop-cancel]').addEventListener('click', () => cleanup(null));
    modal.querySelector('[data-crop-apply]').addEventListener('click', () => {
      const imgRect = img.getBoundingClientRect();
      const boxRect = box.getBoundingClientRect();
      const scaleX = img.naturalWidth / imgRect.width;
      const scaleY = img.naturalHeight / imgRect.height;
      const sx = (boxRect.left - imgRect.left) * scaleX;
      const sy = (boxRect.top - imgRect.top) * scaleY;
      const sw = boxRect.width * scaleX;
      const sh = boxRect.height * scaleY;
      const ratio = Math.min(1, options.maxWidth / sw, options.maxHeight / sh);
      const canvas = document.createElement('canvas');
      canvas.width = Math.round(sw * ratio);
      canvas.height = Math.round(sh * ratio);
      canvas.getContext('2d').drawImage(img, sx, sy, sw, sh, 0, 0, canvas.width, canvas.height);
      canvas.toBlob(blob => {
        cleanup(new File([blob], file.name.replace(/\.[^.]+$/, '.jpg'), { type: 'image/jpeg' }));
      }, 'image/jpeg', 0.86);
    });
  });
}
