const stages = [
  {
    id: 'lead',
    title: '1. 线索与约访',
    desc: '把客户画像、线索评分、需求采集和约访推进合并成一个前端动作。',
    fields: [
      { type: 'checkbox', key: 'industry', label: '客户行业', options: ['科技研发', '电商运营', '设计创意', '区域总部', '轻生产配套', '传统办公'] },
      { type: 'checkbox', key: 'signals', label: '成交信号', options: ['需求面积明确', '预算接近项目价格', '入驻时间3个月内', '决策人可到场', '已有竞品对比', '关注园区形象'] },
      { type: 'checkbox', key: 'needs', label: '已确认需求', options: ['人数规模', '装修要求', '预算范围', '交通要求', '停车需求', '政策关注', '交付时间', '决策链'] },
      { type: 'select', key: 'source', label: '线索来源', options: ['老客户转介绍', '平台招商', '主动电销', '上门咨询', '中介渠道', '活动获客'] },
      { type: 'select', key: 'urgency', label: '推进紧急度', options: ['本周必须看房', '两周内看房', '一个月内比较', '只是初步了解'] },
      { type: 'textarea', key: 'notes', label: '客户原话/补充信息', placeholder: '例：客户目前在周边园区，团队扩张，需要400㎡左右，本周希望看房。' },
    ],
  },
  {
    id: 'space',
    title: '2. 房源匹配与分割建议',
    desc: '根据面积、方位、行业和预算，输出可沟通的分割方向与带看顺序。',
    fields: [
      { type: 'checkbox', key: 'orientation', label: '客户偏好', options: ['靠电梯厅', '采光优先', '独立入口', '靠园区主干道', '安静区域', '可扩租'] },
      { type: 'checkbox', key: 'layout', label: '空间配置', options: ['开放办公区', '独立办公室', '会议室', '前台展示区', '茶水间', '库房/样品间'] },
      { type: 'select', key: 'floorPlate', label: '楼层条件', options: ['整层约2000㎡可分割', '低区楼层', '中高区楼层', '临近已出租区域', '交付状态待确认'] },
      { type: 'textarea', key: 'drawingNotes', label: '图纸/房源备注', placeholder: '例：客户希望300㎡左右，尽量靠东侧采光面，团队约35人。' },
    ],
  },
  {
    id: 'pricing',
    title: '3. 价格测算与报价空间',
    desc: '像Excel一样选择房源、填写客户合同条件，测算破底率、J回正年数和目标报价。',
    fields: [
      { type: 'pricingSheet' },
    ],
  },
  {
    id: 'recap',
    title: '4. 到访录音复盘',
    desc: '粘贴录音转文字或带看纪要，提取关注点、异议、意向等级和下一步动作。',
    fields: [
      { type: 'checkbox', key: 'observations', label: '现场表现', options: ['拍照较多', '询问价格细节', '关注装修期', '询问停车', '老板未到场', '对竞品有比较', '对园区形象认可'] },
      { type: 'textarea', key: 'transcript', label: '录音转文字/带看纪要', placeholder: '粘贴客户现场沟通内容，AI会做复盘摘要。' },
    ],
  },
  {
    id: 'proposal',
    title: '5. 客户方案生成',
    desc: '生成客户版空间方案、商务条件和PPT大纲，方便快速跟进。',
    fields: [
      { type: 'checkbox', key: 'planTypes', label: '方案类型', options: ['成本优先方案', '形象优先方案', '扩展弹性方案', '快速入驻方案'] },
      { type: 'checkbox', key: 'commercial', label: '商务条件', options: ['租金单价', '物业费', '免租期', '押金', '付款周期', '递增方式', '装修支持'] },
      { type: 'checkbox', key: 'comparison', label: '方案增强', options: ['加入综合使用成本对比', '加入竞品适配度对比', '加入交付时间表', '加入扩租路径'] },
      { type: 'textarea', key: 'proposalNotes', label: '方案备注', placeholder: '例：客户希望总成本可控，但老板比较重视接待形象。' },
    ],
  },
  {
    id: 'negotiation',
    title: '6. 谈判话术助手',
    desc: '选择客户异议，生成回应话术、条件交换和底线提醒。',
    fields: [
      { type: 'checkbox', key: 'objections', label: '客户异议', options: ['价格高', '免租期短', '付款周期压力大', '竞品更便宜', '装修成本高', '签约周期不确定', '面积不完全合适'] },
      { type: 'select', key: 'competitor', label: '客户提到的竞品', options: ['未提竞品', '中集美兰城', '地产闵虹·之所智慧方洲', '国盛药谷', '其他'] },
      { type: 'select', key: 'strategy', label: '谈判策略', options: ['稳住价格换条件', '小幅让利换快签', '延长租期换优惠', '付款周期换总价', '上报特批'] },
      { type: 'textarea', key: 'bottomLine', label: '底线/审批备注', placeholder: '例：单价不可低于某底线，免租期超过标准需审批。' },
    ],
  },
  {
    id: 'contract',
    title: '7. 合同草案助手',
    desc: '录入关键商务条件，生成合同字段清单和风险检查，不替代法务审核。',
    fields: [
      { type: 'textarea', key: 'contractTerms', label: '关键条款', placeholder: '例：承租方、房号、面积、单价、物业费、租期、免租期、付款周期、押金。' },
      { type: 'checkbox', key: 'riskChecks', label: '重点检查', options: ['面积前后一致', '价格口径一致', '免租期写清楚', '付款日期明确', '特殊条款需审批', '开票信息完整'] },
    ],
  },
  {
    id: 'dashboard',
    title: '8. 管理看板与复盘沉淀',
    desc: '把单个客户推进动作沉淀为部门管理视角，方便比赛展示经营价值。',
    fields: [
      { type: 'checkbox', key: 'metrics', label: '看板指标', options: ['新增线索', '到访客户', '报价客户', '高意向客户', '待签约客户', '丢单原因', '房源热度', '竞品出现频率', '输赢点复盘'] },
      { type: 'textarea', key: 'reviewNotes', label: '复盘备注', placeholder: '例：本周客户主要卡在预算和装修期，需要准备标准回应。' },
    ],
  },
];

const demoScenario = {
  customer: {
    name: '星澜智能科技有限公司',
    area: '350-500㎡',
    budget: '希望控制总成本，单价可谈',
    moveIn: '2026年6月中旬前',
  },
  lead: {
    industry: ['科技研发'],
    signals: ['需求面积明确', '入驻时间3个月内', '决策人可到场', '已有竞品对比', '关注园区形象'],
    needs: ['人数规模', '装修要求', '预算范围', '交通要求', '停车需求', '交付时间', '决策链'],
    source: '老客户转介绍',
    urgency: '本周必须看房',
    notes: '客户目前在周边园区，团队从28人扩张到45人，需要更好的研发办公形象。已看过一个竞品园区，觉得价格略低但楼栋形象一般。',
  },
  space: {
    orientation: ['采光优先', '独立入口', '可扩租'],
    layout: ['开放办公区', '独立办公室', '会议室', '前台展示区', '茶水间'],
    floorPlate: '整层约2000㎡可分割',
    drawingNotes: '整层约2000㎡，客户适合从东南侧采光面切出约420㎡。建议保留靠近电梯厅的展示面，后续旁边预留约150㎡扩租弹性。',
  },
  pricing: {
    approvedArea: '76',
    approvedPrice: '2.61617688166994',
    contractArea: '88',
    contractPrice: '1.6',
    leaseYears: '3',
    contractEscalation: '0',
    approvedFreePattern: '1,1,1',
    contractFreePattern: '2,1,0',
    approvedPropertyFee: '12',
    contractPropertyFee: '12',
    fitoutCost: '0',
    partitionCost: '10000',
    costArea: '88',
    specialItems: '0',
    targetJYears: '10',
  },
  recap: {
    observations: ['拍照较多', '询问价格细节', '关注装修期', '询问停车', '对竞品有比较', '对园区形象认可'],
    transcript: '客户到访后表示：这个楼栋形象比之前看的竞品好，采光也不错。主要担心两个点：第一是价格能不能再优化，第二是装修和交付时间能不能赶上6月。客户老板比较关注前台形象和会议室数量，财务负责人会看付款周期。',
  },
  proposal: {
    planTypes: ['成本优先方案', '形象优先方案', '扩展弹性方案'],
    commercial: ['租金单价', '物业费', '免租期', '押金', '付款周期', '装修支持'],
    comparison: ['加入综合使用成本对比', '加入竞品适配度对比', '加入交付时间表'],
    proposalNotes: '建议给客户三版：380㎡成本控制版、420㎡形象均衡版、500㎡扩展弹性版。重点强调采光、前台展示面、快速交付和后续扩租。',
  },
  negotiation: {
    objections: ['价格高', '免租期短', '付款周期压力大', '竞品更便宜', '装修成本高'],
    competitor: '中集美兰城',
    strategy: '小幅让利换快签',
    bottomLine: '价格不能直接打到底。可用较快签约、两年期以上、付款周期稳定来换取适度优惠；超过标准免租期需上报审批。',
  },
  contract: {
    contractTerms: '承租方：星澜智能科技有限公司；房源：MAX科技园某楼某层东南侧分割单元；面积：约420㎡，最终以合同附件图纸和实测/约定面积为准；租期：24个月；计划起租：2026年6月；付款周期：押二付三；免租期：按审批结果填写；物业费、开票信息和特殊约定待补充。',
    riskChecks: ['面积前后一致', '价格口径一致', '免租期写清楚', '付款日期明确', '特殊条款需审批', '开票信息完整'],
  },
  dashboard: {
    metrics: ['新增线索', '到访客户', '报价客户', '高意向客户', '待签约客户', '丢单原因', '房源热度', '竞品出现频率', '输赢点复盘'],
    reviewNotes: '本周科技研发类客户关注点集中在总成本、装修交付、停车和园区形象。建议把400-500㎡可快速交付房源做成标准推荐包，并沉淀价格异议回应话术。',
  },
};

const state = { stage: stages[0].id };
const root = document.querySelector('.app');
const csrf = root?.dataset.csrf || '';
const aiMeta = JSON.parse(root?.dataset.aiMeta || '{"active":"deepseek","providers":{}}');
const nav = document.querySelector('.flow-nav');
const fields = document.querySelector('#stageFields');
const title = document.querySelector('#stageTitle');
const desc = document.querySelector('#stageDesc');
const resultBox = document.querySelector('#resultBox');
const liveScore = document.querySelector('#liveScore');
const knowledgeModal = document.querySelector('#knowledgeModal');
let knowledgeMeta = null;
let knowledgeItems = [];
let projectItems = [];

function renderProviderSelect() {
  const select = document.querySelector('#providerSelect');
  if (!select) return;
  select.innerHTML = Object.entries(aiMeta.providers || {}).map(([key, provider]) => {
    const status = key === 'demo' ? '本地兜底' : (provider.configured ? provider.model : `${provider.model} / 演示`);
    return `<option value="${escapeHtml(key)}">${escapeHtml(provider.label)} · ${escapeHtml(status)}</option>`;
  }).join('');
  select.value = aiMeta.active || 'deepseek';
}

function renderNav() {
  nav.innerHTML = stages.map((stage) => `
    <button class="flow-item ${stage.id === state.stage ? 'active' : ''}" data-stage="${stage.id}">
      <span>${stage.title.split('.')[0]}</span>
      ${stage.title.replace(/^\d+\.\s*/, '')}
    </button>
  `).join('');
}

function fieldHtml(field) {
  if (field.type === 'pricingSheet') {
    const rooms = (window.MAX2GO_DATA?.rooms || []).map((room, index) => `
      <option value="${index}">${room.building} / ${room.room} / ${room.area}㎡ / ${room.price} / ${room.status}</option>
    `).join('');
    return `
      <div class="pricing-sheet">
        <div class="pricing-toolbar">
          <label>
            调用Excel房源
            <select id="pricingRoomSelect" data-key="roomIndex" data-type="select">${rooms}</select>
          </label>
          <button class="secondary-btn" id="loadRoomBtn" type="button">载入房源</button>
        </div>
        <table class="excel-table">
          <thead>
            <tr>
              <th>项目</th>
              <th>已报批一房一价/租决</th>
              <th>现租约/客户条件</th>
              <th>差额/说明</th>
            </tr>
          </thead>
          <tbody>
            <tr><td>房号</td><td><input data-key="roomCode" data-type="text"></td><td><input data-key="contractRoomCode" data-type="text"></td><td class="muted-cell">自动同步，可手动改</td></tr>
            <tr><td>面积/㎡</td><td><input data-key="approvedArea" data-type="number" type="number" step="any"></td><td><input data-key="contractArea" data-type="number" type="number" step="any"></td><td id="diffArea">-</td></tr>
            <tr><td>单价/元/㎡·天</td><td><input data-key="approvedPrice" data-type="number" type="number" step="any"></td><td><input data-key="contractPrice" data-type="number" type="number" step="any"></td><td id="diffPrice">-</td></tr>
            <tr><td>物业费/元/㎡·月</td><td><input data-key="approvedPropertyFee" data-type="number" type="number" step="any" value="12"></td><td><input data-key="contractPropertyFee" data-type="number" type="number" step="any" value="12"></td><td id="diffPropertyFee">-</td></tr>
            <tr><td>租金递增</td><td><input data-key="approvedEscalation" data-type="number" type="number" step="any" value="5"></td><td><input data-key="contractEscalation" data-type="number" type="number" step="any" value="0"></td><td class="muted-cell">填写百分比，如5</td></tr>
            <tr><td>免租期/月</td><td><input data-key="approvedFreePattern" data-type="text" value="1,1,1"></td><td><input data-key="contractFreePattern" data-type="text" value="2,1,0"></td><td class="muted-cell">按年填写，如2,1,0</td></tr>
            <tr><td>租期/年</td><td><input data-key="leaseYears" data-type="number" type="number" step="1" value="3"></td><td><input data-key="targetJYears" data-type="number" type="number" step="any" value="10"></td><td class="muted-cell">右侧为目标J回正年数</td></tr>
            <tr><td>精装成本/元</td><td colspan="2"><input data-key="fitoutCost" data-type="number" type="number" step="any" value="0"></td><td>分摊进租决价格</td></tr>
            <tr><td>分户改造/元</td><td colspan="2"><input data-key="partitionCost" data-type="number" type="number" step="any" value="10000"></td><td>分摊进租决价格</td></tr>
            <tr><td>成本分摊面积/㎡</td><td colspan="2"><input data-key="costArea" data-type="number" type="number" step="any"></td><td>默认合同面积</td></tr>
            <tr><td>特殊事项/元</td><td colspan="2"><input data-key="specialItems" data-type="number" type="number" step="any" value="0"></td><td>如车位减免，减少收入填负数</td></tr>
          </tbody>
        </table>
        <div class="j-table-preview">
          <strong>J回正表已调用</strong>
          <span>来自你上传的Excel：破底率 0% 至 -50%，对应 J回正约 8.26 年至 14.24 年。</span>
        </div>
      </div>
    `;
  }
  if (field.type === 'checkbox') {
    return `
      <fieldset class="field-block" data-key="${field.key}" data-type="checkbox">
        <legend>${field.label}</legend>
        <div class="check-grid">
          ${field.options.map((option) => `
            <label class="check-pill">
              <input type="checkbox" value="${option}">
              <span>${option}</span>
            </label>
          `).join('')}
        </div>
      </fieldset>
    `;
  }
  if (field.type === 'select') {
    return `
      <label class="field-block">
        <span>${field.label}</span>
        <select data-key="${field.key}" data-type="select">
          ${field.options.map((option) => `<option value="${option}">${option}</option>`).join('')}
        </select>
      </label>
    `;
  }
  if (field.type === 'number' || field.type === 'text') {
    return `
      <label class="field-block">
        <span>${field.label}</span>
        <input data-key="${field.key}" data-type="${field.type}" type="${field.type === 'number' ? 'number' : 'text'}" step="any" placeholder="${field.placeholder || ''}">
      </label>
    `;
  }
  return `
    <label class="field-block wide">
      <span>${field.label}</span>
      <textarea data-key="${field.key}" data-type="textarea" placeholder="${field.placeholder || ''}"></textarea>
    </label>
  `;
}

function renderStage() {
  const stage = stages.find((item) => item.id === state.stage);
  title.textContent = stage.title;
  desc.textContent = stage.desc;
  fields.innerHTML = stage.fields.map(fieldHtml).join('');
  renderNav();
  if (state.stage === 'pricing') {
    wirePricingSheet();
  }
  updateScore();
}

function setPricingValue(key, value) {
  const node = fields.querySelector(`[data-key="${key}"]`);
  if (node) node.value = value ?? '';
}

function getPricingValue(key) {
  const node = fields.querySelector(`[data-key="${key}"]`);
  return node ? node.value : '';
}

function loadSelectedRoom() {
  const select = document.querySelector('#pricingRoomSelect');
  const rooms = window.MAX2GO_DATA?.rooms || [];
  const room = rooms[Number(select?.value || 0)];
  if (!room) return;
  setPricingValue('roomCode', room.room);
  setPricingValue('contractRoomCode', room.room);
  setPricingValue('approvedArea', room.area);
  setPricingValue('approvedPrice', room.price);
  if (!getPricingValue('contractArea')) setPricingValue('contractArea', room.area);
  if (!getPricingValue('contractPrice')) setPricingValue('contractPrice', room.price);
  if (!getPricingValue('costArea')) setPricingValue('costArea', getPricingValue('contractArea') || room.area);
  updatePricingDiffs();
}

function updatePricingDiffs() {
  if (state.stage !== 'pricing') return;
  const approvedArea = Number(getPricingValue('approvedArea') || 0);
  const contractArea = Number(getPricingValue('contractArea') || 0);
  const approvedPrice = Number(getPricingValue('approvedPrice') || 0);
  const contractPrice = Number(getPricingValue('contractPrice') || 0);
  const approvedProperty = Number(getPricingValue('approvedPropertyFee') || 0);
  const contractProperty = Number(getPricingValue('contractPropertyFee') || 0);
  const areaNode = document.querySelector('#diffArea');
  const priceNode = document.querySelector('#diffPrice');
  const propertyNode = document.querySelector('#diffPropertyFee');
  if (areaNode) areaNode.textContent = decimal(contractArea - approvedArea);
  if (priceNode) priceNode.textContent = decimal(contractPrice - approvedPrice, 3);
  if (propertyNode) propertyNode.textContent = decimal(contractProperty - approvedProperty, 2);
}

function wirePricingSheet() {
  document.querySelector('#loadRoomBtn')?.addEventListener('click', loadSelectedRoom);
  document.querySelector('#pricingRoomSelect')?.addEventListener('change', loadSelectedRoom);
  fields.querySelectorAll('input, select').forEach((node) => node.addEventListener('input', updatePricingDiffs));
  loadSelectedRoom();
}

function setCustomer(customer) {
  document.querySelector('#customerName').value = customer.name;
  document.querySelector('#customerArea').value = customer.area;
  document.querySelector('#customerBudget').value = customer.budget;
  document.querySelector('#customerMoveIn').value = customer.moveIn;
}

function applyStageValues(stageId) {
  const values = demoScenario[stageId] || {};
  fields.querySelectorAll('[data-key]').forEach((node) => {
    const key = node.dataset.key;
    const type = node.dataset.type;
    const value = values[key];
    if (type === 'checkbox') {
      node.querySelectorAll('input').forEach((input) => {
        input.checked = Array.isArray(value) && value.includes(input.value);
      });
      return;
    }
    if (typeof value === 'string') {
      node.value = value;
    }
  });
  updateScore();
}

function loadDemoScenario() {
  setCustomer(demoScenario.customer);
  applyStageValues(state.stage);
  resultBox.dataset.raw = '';
  resultBox.innerHTML = '已载入演示客户“星澜智能科技有限公司”。现在可以直接点击“生成AI建议”；切换其他流程模块后，再点一次“载入演示客户”会填入对应模块的示例信息。';
}

function collectPayload() {
  const projectSelect = document.querySelector('#projectSelect');
  const project = {
    key: projectSelect.value,
    name: projectSelect.options[projectSelect.selectedIndex].textContent,
  };
  const customer = {
    name: document.querySelector('#customerName').value,
    area: document.querySelector('#customerArea').value,
    budget: document.querySelector('#customerBudget').value,
    moveIn: document.querySelector('#customerMoveIn').value,
  };

  const inputs = {};
  fields.querySelectorAll('[data-key]').forEach((node) => {
    const key = node.dataset.key;
    const type = node.dataset.type;
    if (type === 'checkbox') {
      inputs[key] = [...node.querySelectorAll('input:checked')].map((input) => input.value);
    } else {
      inputs[key] = node.value;
    }
  });

  const provider = document.querySelector('#providerSelect')?.value || aiMeta.active || 'deepseek';
  return { csrf, stage: state.stage, provider, project, customer, inputs };
}

function updateScore() {
  if (state.stage !== 'lead') {
    liveScore.textContent = '流程模块';
    return;
  }
  const checked = fields.querySelectorAll('input:checked').length;
  const customerFilled = ['#customerName', '#customerArea', '#customerBudget', '#customerMoveIn']
    .filter((selector) => document.querySelector(selector).value.trim() !== '').length;
  const score = Math.min(100, checked * 10 + customerFilled * 8);
  liveScore.textContent = `线索评分 ${score}`;
}

function markdownToHtml(text) {
  return text
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/^### (.*)$/gm, '<h3>$1</h3>')
    .replace(/^\*\*(.*?)\*\*$/gm, '<h4>$1</h4>')
    .replace(/^- (.*)$/gm, '<li>$1</li>')
    .replace(/(<li>.*<\/li>)/gs, '<ul>$1</ul>')
    .replace(/\n{2,}/g, '</p><p>')
    .replace(/\n/g, '<br>');
}

function escapeHtml(value) {
  return String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

async function knowledgeRequest(payload) {
  const res = await fetch('api/knowledge.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ csrf, ...payload }),
  });
  const data = await res.json();
  if (!res.ok) throw new Error(data.error || '知识库操作失败');
  return data;
}

function renderProjectOptions(projects) {
  const html = Object.entries(projects).map(([key, name]) => `<option value="${escapeHtml(key)}">${escapeHtml(name)}</option>`).join('');
  const currentProject = document.querySelector('#projectSelect').value || 'meilanhu';
  const currentKnowledgeProject = document.querySelector('#knowledgeProject').value || currentProject;
  document.querySelector('#projectSelect').innerHTML = html;
  document.querySelector('#knowledgeProject').innerHTML = html;
  document.querySelector('#projectSelect').value = projects[currentProject] ? currentProject : Object.keys(projects)[0];
  document.querySelector('#knowledgeProject').value = projects[currentKnowledgeProject] ? currentKnowledgeProject : document.querySelector('#projectSelect').value;
}

function renderKnowledgeStages(stagesMeta) {
  const box = document.querySelector('#knowledgeStages');
  box.innerHTML = Object.entries(stagesMeta).map(([key, label]) => `
    <label class="check-pill">
      <input type="checkbox" value="${key}" ${key === 'all' ? 'checked' : ''}>
      <span>${label}</span>
    </label>
  `).join('');
}

async function loadKnowledgeMeta() {
  if (knowledgeMeta) return knowledgeMeta;
  knowledgeMeta = await knowledgeRequest({ action: 'meta' });
  renderProjectOptions(knowledgeMeta.projects);
  const typeSelect = document.querySelector('#knowledgeType');
  typeSelect.innerHTML = knowledgeMeta.types.map((type) => `<option value="${type}">${type}</option>`).join('');
  renderKnowledgeStages(knowledgeMeta.stages);
  return knowledgeMeta;
}

async function refreshProjectList() {
  const data = await knowledgeRequest({ action: 'project_list' });
  projectItems = data.items || [];
  renderProjectList();
}

function renderProjectList() {
  const list = document.querySelector('#projectList');
  if (!projectItems.length) {
    list.innerHTML = '<p class="muted-text">暂无项目。保存一个新项目后，会出现在工作台顶部。</p>';
    return;
  }
  list.innerHTML = projectItems.map((item) => `
    <article class="project-item">
      <div>
        <strong>${escapeHtml(item.name)}</strong>
        <p>${escapeHtml(item.project_key)} · ${item.enabled === 1 || item.enabled === '1' ? '已启用' : '已停用'} · 排序${escapeHtml(item.sort_order)}</p>
      </div>
      <div class="knowledge-item-actions">
        <button class="secondary-btn" type="button" data-project-edit="${item.id}">编辑</button>
        <button class="secondary-btn" type="button" data-project-delete="${item.id}">删除</button>
      </div>
    </article>
  `).join('');
}

function resetProjectForm() {
  document.querySelector('#projectId').value = '';
  document.querySelector('#projectKey').value = '';
  document.querySelector('#projectName').value = '';
  document.querySelector('#projectSort').value = '100';
  document.querySelector('#projectEnabled').checked = true;
}

function editProjectItem(id) {
  const item = projectItems.find((entry) => Number(entry.id) === Number(id));
  if (!item) return;
  document.querySelector('#projectId').value = item.id;
  document.querySelector('#projectKey').value = item.project_key;
  document.querySelector('#projectName').value = item.name;
  document.querySelector('#projectSort').value = item.sort_order;
  document.querySelector('#projectEnabled').checked = item.enabled === 1 || item.enabled === '1';
}

async function saveProjectItem(event) {
  event.preventDefault();
  const data = await knowledgeRequest({
    action: 'project_save',
    id: document.querySelector('#projectId').value,
    project_key: document.querySelector('#projectKey').value,
    name: document.querySelector('#projectName').value,
    sort_order: document.querySelector('#projectSort').value,
    enabled: document.querySelector('#projectEnabled').checked,
  });
  knowledgeMeta = null;
  if (data.projects) renderProjectOptions(data.projects);
  await refreshProjectList();
  resetProjectForm();
}

function resetKnowledgeForm() {
  document.querySelector('#knowledgeId').value = '';
  document.querySelector('#knowledgeProject').value = document.querySelector('#projectSelect').value;
  document.querySelector('#knowledgeType').value = '项目概况';
  document.querySelector('#knowledgeTitleInput').value = '';
  document.querySelector('#knowledgePriority').value = '2';
  document.querySelector('#knowledgeEnabled').checked = true;
  document.querySelector('#knowledgeContent').value = '';
  document.querySelectorAll('#knowledgeStages input').forEach((input) => {
    input.checked = input.value === 'all';
  });
}

function renderKnowledgeList() {
  const list = document.querySelector('#knowledgeList');
  if (!knowledgeItems.length) {
    list.innerHTML = '<p class="muted-text">当前项目还没有维护资料。可以先点“导入美兰湖示例”，或直接粘贴一条新资料。</p>';
    return;
  }

  list.innerHTML = knowledgeItems.map((item) => `
    <article class="knowledge-item" data-id="${item.id}">
      <div class="knowledge-item-head">
        <div>
          <strong>${escapeHtml(item.title)}</strong>
          <p>${escapeHtml(item.type)} · ${item.enabled === 1 || item.enabled === '1' ? '已启用' : '已停用'} · 优先级${escapeHtml(item.priority)}</p>
        </div>
        <div class="knowledge-item-actions">
          <button class="secondary-btn" type="button" data-edit="${item.id}">编辑</button>
          <button class="secondary-btn" type="button" data-delete="${item.id}">删除</button>
        </div>
      </div>
      <p>${escapeHtml(String(item.content || '').slice(0, 120))}${String(item.content || '').length > 120 ? '...' : ''}</p>
    </article>
  `).join('');
}

async function refreshKnowledgeList() {
  const projectKey = document.querySelector('#knowledgeProject').value || document.querySelector('#projectSelect').value;
  const data = await knowledgeRequest({ action: 'list', project_key: projectKey });
  knowledgeItems = data.items || [];
  renderKnowledgeList();
}

function editKnowledgeItem(id) {
  const item = knowledgeItems.find((entry) => Number(entry.id) === Number(id));
  if (!item) return;
  document.querySelector('#knowledgeId').value = item.id;
  document.querySelector('#knowledgeProject').value = item.project_key;
  document.querySelector('#knowledgeType').value = item.type;
  document.querySelector('#knowledgeTitleInput').value = item.title;
  document.querySelector('#knowledgePriority').value = String(item.priority);
  document.querySelector('#knowledgeEnabled').checked = item.enabled === 1 || item.enabled === '1';
  document.querySelector('#knowledgeContent').value = item.content;
  const stages = String(item.stages || 'all').split(',');
  document.querySelectorAll('#knowledgeStages input').forEach((input) => {
    input.checked = stages.includes(input.value);
  });
}

async function saveKnowledgeItem(event) {
  event.preventDefault();
  const stages = [...document.querySelectorAll('#knowledgeStages input:checked')].map((input) => input.value);
  await knowledgeRequest({
    action: 'save',
    id: document.querySelector('#knowledgeId').value,
    project_key: document.querySelector('#knowledgeProject').value,
    type: document.querySelector('#knowledgeType').value,
    title: document.querySelector('#knowledgeTitleInput').value,
    priority: document.querySelector('#knowledgePriority').value,
    enabled: document.querySelector('#knowledgeEnabled').checked,
    content: document.querySelector('#knowledgeContent').value,
    stages,
  });
  await refreshKnowledgeList();
  resetKnowledgeForm();
}

async function openKnowledgeModal() {
  knowledgeModal.classList.add('open');
  knowledgeModal.setAttribute('aria-hidden', 'false');
  try {
    await loadKnowledgeMeta();
    resetKnowledgeForm();
    resetProjectForm();
    await refreshProjectList();
    await refreshKnowledgeList();
  } catch (error) {
    document.querySelector('#knowledgeList').innerHTML = `<p class="form-error">${error.message}</p>`;
  }
}

function closeKnowledgeModal() {
  knowledgeModal.classList.remove('open');
  knowledgeModal.setAttribute('aria-hidden', 'true');
}

async function generate() {
  const button = document.querySelector('#generateBtn');
  button.disabled = true;
  button.textContent = '生成中...';
  resultBox.innerHTML = '<p>正在整理业务信息并生成建议。</p>';

  try {
    if (state.stage === 'pricing') {
      await calculatePricing();
      return;
    }
    const res = await fetch('api/generate.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(collectPayload()),
    });
    const data = await res.json();
    if (!res.ok) throw new Error(data.error || '生成失败');
    resultBox.dataset.raw = data.content;
    resultBox.innerHTML = markdownToHtml(data.content);
    if (data.demo) {
      const reason = data.provider === 'demo' ? '本地演示结果，不调用外部 API。' : `${escapeHtml(data.providerLabel || '模型接口')} 未配置 API Key。`;
      resultBox.innerHTML += `<p class="demo-note">当前为演示模式：${reason}</p>`;
    } else if (data.providerLabel && data.model) {
      resultBox.innerHTML += `<p class="demo-note">由 ${escapeHtml(data.providerLabel)} / ${escapeHtml(data.model)} 生成。</p>`;
    }
  } catch (error) {
    resultBox.textContent = error.message;
  } finally {
    button.disabled = false;
    button.textContent = '生成AI建议';
  }
}

function money(value) {
  return Number(value || 0).toLocaleString('zh-CN', { maximumFractionDigits: 0 });
}

function decimal(value, digits = 2) {
  return Number(value || 0).toLocaleString('zh-CN', { minimumFractionDigits: digits, maximumFractionDigits: digits });
}

function percent(value) {
  return `${(Number(value || 0) * 100).toFixed(2)}%`;
}

async function calculatePricing() {
  const res = await fetch('api/price.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(collectPayload()),
  });
  const data = await res.json();
  if (!res.ok) throw new Error(data.error || '测算失败');
  const r = data.result;
  const status = r.breakRate >= 0 ? '未破底' : `破底 ${percent(r.breakRate)}`;
  const raw = `价格测算结果
租决面积：${decimal(r.approvedArea)}㎡
租决价格：${decimal(r.approvedPrice, 3)} 元/㎡/天
成本折算溢价：${decimal(r.costPremium, 3)} 元/㎡/天
有效租决价格：${decimal(r.effectiveApprovedPrice, 3)} 元/㎡/天
合同面积：${decimal(r.contractArea)}㎡
合同价格：${decimal(r.contractPrice, 3)} 元/㎡/天
租决租金总额：${money(r.approvedRent)} 元
客户租金总额：${money(r.contractRent)} 元
物业收入差额：${money(r.propertyDiff)} 元
特殊事项：${money(r.specialItems)} 元
综合破底率：${percent(r.breakRate)}
预计J回正年数：${decimal(r.jYears)} 年
目标J回正年数：${decimal(r.targetJYears)} 年
目标破底率：${percent(r.targetBreakRate)}
目标合同单价：${decimal(r.targetContractPrice, 3)} 元/㎡/天`;

  resultBox.dataset.raw = raw;
  resultBox.innerHTML = `
    <h3>价格测算结果：${status}</h3>
    <div class="calc-grid">
      <div><span>预计J回正</span><strong>${decimal(r.jYears)} 年</strong></div>
      <div><span>综合破底率</span><strong>${percent(r.breakRate)}</strong></div>
      <div><span>目标合同单价</span><strong>${decimal(r.targetContractPrice, 3)}</strong></div>
      <div><span>当前合同单价</span><strong>${decimal(r.contractPrice, 3)}</strong></div>
    </div>
    <table class="excel-table output-table">
      <thead><tr><th>项目</th><th>租决口径</th><th>客户合同口径</th><th>差额/结果</th></tr></thead>
      <tbody>
        <tr><td>面积/㎡</td><td>${decimal(r.approvedArea)}</td><td>${decimal(r.contractArea)}</td><td>${decimal(r.contractArea - r.approvedArea)}</td></tr>
        <tr><td>单价/元/㎡·天</td><td>${decimal(r.approvedPrice, 3)}</td><td>${decimal(r.contractPrice, 3)}</td><td>${decimal(r.contractPrice - r.approvedPrice, 3)}</td></tr>
        <tr><td>成本折算溢价</td><td>${decimal(r.costPremium, 3)}</td><td>-</td><td>有效租决价 ${decimal(r.effectiveApprovedPrice, 3)}</td></tr>
        <tr><td>合同周期租金总额</td><td>${money(r.approvedRent)}</td><td>${money(r.contractRent)}</td><td>${money(r.rentDiff)}</td></tr>
        <tr><td>合同周期物业总额</td><td>${money(r.approvedProperty)}</td><td>${money(r.contractProperty)}</td><td>${money(r.propertyDiff)}</td></tr>
        <tr><td>特殊事项</td><td>-</td><td>${money(r.specialItems)}</td><td>${money(r.specialItems)}</td></tr>
        <tr><td>综合破底率</td><td colspan="2">按Excel公式：(租金差额 + 物业差额 + 特殊事项) / 租决租金总额</td><td><strong>${percent(r.breakRate)}</strong></td></tr>
        <tr><td>J回正年数</td><td colspan="2">调用Excel右侧破底率-J回正表插值</td><td><strong>${decimal(r.jYears)} 年</strong></td></tr>
      </tbody>
    </table>
    <h4>报价空间</h4>
    <p>若希望控制在 ${decimal(r.targetJYears)} 年J回正附近，按当前面积、免租和涨幅条件反推，合同单价约为 <strong>${decimal(r.targetContractPrice, 3)} 元/㎡/天</strong>。实际对外报价建议结合客户决策速度、付款周期、免租期和审批底线再做调整。</p>
  `;
}

nav.addEventListener('click', (event) => {
  const button = event.target.closest('[data-stage]');
  if (!button) return;
  state.stage = button.dataset.stage;
  renderStage();
});

document.addEventListener('input', updateScore);
document.querySelector('#generateBtn').addEventListener('click', generate);
document.querySelector('#demoBtn').addEventListener('click', loadDemoScenario);
document.querySelector('#knowledgeBtn').addEventListener('click', openKnowledgeModal);
document.querySelectorAll('[data-close-modal]').forEach((node) => node.addEventListener('click', closeKnowledgeModal));
document.querySelector('#knowledgeForm').addEventListener('submit', saveKnowledgeItem);
document.querySelector('#projectForm').addEventListener('submit', saveProjectItem);
document.querySelector('#projectResetBtn').addEventListener('click', resetProjectForm);
document.querySelector('#knowledgeResetBtn').addEventListener('click', resetKnowledgeForm);
document.querySelector('#refreshKnowledgeBtn').addEventListener('click', refreshKnowledgeList);
document.querySelector('#knowledgeProject').addEventListener('change', refreshKnowledgeList);
document.querySelector('#seedMeilanhuBtn').addEventListener('click', async () => {
  await knowledgeRequest({ action: 'seed_meilanhu' });
  document.querySelector('#knowledgeProject').value = 'meilanhu';
  await refreshKnowledgeList();
});
document.querySelector('#knowledgeList').addEventListener('click', async (event) => {
  const editId = event.target.closest('[data-edit]')?.dataset.edit;
  const deleteId = event.target.closest('[data-delete]')?.dataset.delete;
  if (editId) editKnowledgeItem(editId);
  if (deleteId && confirm('确定删除这条资料吗？')) {
    await knowledgeRequest({ action: 'delete', id: deleteId });
    await refreshKnowledgeList();
  }
});
document.querySelector('#projectList').addEventListener('click', async (event) => {
  const editId = event.target.closest('[data-project-edit]')?.dataset.projectEdit;
  const deleteId = event.target.closest('[data-project-delete]')?.dataset.projectDelete;
  if (editId) editProjectItem(editId);
  if (deleteId && confirm('确定删除这个项目吗？项目下的知识库资料不会自动删除。')) {
    const data = await knowledgeRequest({ action: 'project_delete', id: deleteId });
    knowledgeMeta = null;
    if (data.projects) renderProjectOptions(data.projects);
    await refreshProjectList();
    await refreshKnowledgeList();
  }
});
document.querySelector('#copyBtn').addEventListener('click', async () => {
  const raw = resultBox.dataset.raw || resultBox.innerText;
  await navigator.clipboard.writeText(raw);
  document.querySelector('#copyBtn').textContent = '已复制';
  setTimeout(() => { document.querySelector('#copyBtn').textContent = '复制'; }, 1200);
});

renderProviderSelect();
renderStage();
loadKnowledgeMeta().catch(() => {});
