const stages = [
  {
    id: 'tour',
    title: '1. 带看实战包 (破冰与推盘)',
    desc: '根据客户画像快速输出主推房源、破冰话术和竞品防坑提示。',
    fields: [
      { type: 'checkbox', key: 'signals', label: '客户画像', options: ['需求面积明确', '预算接近底价', '急用且带决策人', '比较过周边竞品', '极度在意成本', '非常看重形象'] },
      { type: 'select', key: 'competitor', label: '竞品倾向', options: ['未提及', '中集美兰城', '闵虹·之所智慧方洲', '国盛药谷', '周边其他低价厂房'] },
      { type: 'textarea', key: 'notes', label: '客户原话/特别关注', placeholder: '例：客户带了财务来，感觉一直在算单价，但也提到想有个体面的前台。' },
    ],
  },
  {
    id: 'objection',
    title: '2. 异议处理与逼定',
    desc: '针对后期的价格拉扯、竞品对比，生成实战答复话术和向上要政策的理由。',
    fields: [
      { type: 'checkbox', key: 'objections', label: '核心卡点', options: ['嫌租金单价贵', '想要长免租期', '觉得物业费高', '嫌停车费贵', '对交付时间有疑虑', '还要回去请示老板'] },
      { type: 'select', key: 'strategy', label: '我方推进策略', options: ['用交付快打他', '用形象好打他', '用综合成本(物业空调)打他', '愿意让步换今天定'] },
      { type: 'textarea', key: 'bottomLine', label: '业务员心里话', placeholder: '例：其实我可以给他多一个月免租，但我想先稳住单价。' },
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
    desc: '粘贴或上传录音转文字文本，提取客户关注点、异议、意向等级、下一步动作和跟进微信。',
    fields: [
      { type: 'checkbox', key: 'observations', label: '现场表现', options: ['拍照较多', '询问价格细节', '关注装修期', '询问停车', '老板未到场', '对竞品有比较', '对园区形象认可'] },
      { type: 'select', key: 'visitorRole', label: '到访角色', options: ['未确认', '老板/决策人', '财务负责人', '行政/经办人', '代理/中介', '多人到访'] },
      { type: 'select', key: 'dealStage', label: '推进阶段', options: ['初次到访', '二次到访', '比价阶段', '准备报价', '等老板拍板', '准备合同'] },
      { type: 'select', key: 'recapFocus', label: '复盘重点', options: ['综合判断', '意向等级', '价格异议', '下一步逼单', '微信跟进话术'] },
      { type: 'transcriptUpload' },
      { type: 'textarea', key: 'transcript', label: '录音转文字内容/客户原话/带看纪要', placeholder: '可直接粘贴转文字平台输出的文本。建议保留客户原话、价格/交付/面积等关键问答，不必粘贴整段无关寒暄。' },
    ],
  },
  {
    id: 'contract',
    title: '5. 合同草案助手',
    desc: '录入承租方、房源、租期和商务条件，生成合同字段清单，也可以基于标准模板生成可下载合同。',
    fields: [
      { type: 'contractBuilder' },
      { type: 'checkbox', key: 'riskChecks', label: '重点检查', options: ['面积前后一致', '价格口径一致', '免租期写清楚', '付款日期明确', '特殊条款需审批', '开票信息完整'] },
    ],
  },
  {
    id: 'floorplan',
    title: '6. AI 室内平面设计',
    desc: '输入空间排布要求，AI产出文字版空间规划建议。如有平面图可在此上传预览（仅作参考）。下方可直接前往第三方工具生成可视化设计。',
    fields: [
      { type: 'imageUploadHint' },
      { type: 'checkbox', key: 'requirements', label: '空间排布重点', options: ['最大化员工工位', '老板办公室要气派', '需要多个封闭会议室', '前台展示要宽敞', '休闲茶水间不可少'] },
      { type: 'textarea', key: 'floorplanNotes', label: '图纸特征与特殊说明', placeholder: '例：客户想要开放式办公。图纸呈长方形，大门在正南，北侧是玻璃幕墙，承重墙集中在电梯厅周围。' },
      { type: 'designTools' }
    ],
  },
  {
    id: 'video',
    title: '7. 短视频获客助手',
    desc: '涵盖爆款脚本、同城拆解、截流回复与矩阵裂变，全链路助力短视频获客转化。',
    fields: [
      { type: 'select', key: 'videoCategory', label: '视频类别', options: ['房源展示 / 带看实录', '网络热梗 / 剧情段子', '政策解读 / 行业观点', '招商日常 / 个人IP建立'] },
      { type: 'select', key: 'videoAction', label: '核心诉求', options: ['未发布：求爆款脚本与拍摄建议', '未发布：求短内容矩阵裂变 (一鱼多吃)', '灵感：同城爆款视频对标拆解', '已发布：播放惨淡求诊断', '已发布：播放不错但无留资', '评论区：求高情商回复/同行截流词典'] },
      { type: 'checkbox', key: 'videoTarget', label: '受众/房源特征 (房源类必选)', options: ['强调极致低价', '豪华装修拎包入住', '交通地铁便利', '适合科技研发', '老板换租看重面子'] },
      { type: 'textarea', key: 'videoInput', label: '附加信息 / 数据反馈 / 评论粘贴', placeholder: '例1：我要拍个“房东直租VS中介”的搞笑段子\n例2：播放量500，点赞2个，如何改进？\n例3：客户评论“租不起，太贵了”，怎么回？' },
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
  tour: {
    signals: ['需求面积明确', '急用且带决策人', '比较过周边竞品'],
    competitor: '中集美兰城',
    notes: '客户带了财务来，感觉一直在算单价，但也提到想有个体面的前台。团队大约40人。',
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
    visitorRole: '多人到访',
    dealStage: '比价阶段',
    recapFocus: '综合判断',
    transcript: '客户：这个楼栋形象比之前看的竞品好，采光也不错。我们老板比较关注前台形象和会议室数量，财务这边会看付款周期。\n业务员：您现在主要担心哪几个点？\n客户：第一是价格能不能再优化，第二是装修和交付时间能不能赶上6月。如果这两个能解决，我们可以再约老板过来定一下最终面积。',
  },
  objection: {
    objections: ['嫌租金单价贵', '还要回去请示老板'],
    strategy: '用综合成本(物业空调)打他',
    bottomLine: '我可以去跟总监申请多半个月免租期，但单价必须咬住在2.3以上。',
  },
  contract: {
    tenantName: '星澜智能科技有限公司',
    creditCode: '91310000MA1MAX2GO1',
    registeredAddress: '上海市宝山区罗店镇示例路88号',
    legalRepresentative: '陈星澜',
    tenantPhone: '13800000000',
    contactPerson: '陈星澜',
    signYear: '2026',
    signMonth: '',
    signDay: '',
    propertyAddress: '上海市宝山区罗店路388弄33号B座706室',
    roomCode: 'B座706室',
    area: '240',
    leaseMonths: '36',
    unitPrice: '1.6',
    propertyUnitFee: '12',
    escalationRate: '5',
    fitoutPattern: '2,1,0',
    firstRentMonths: '3',
    depositMonths: '3',
    leaseStart: '2026-06-01',
    leaseEnd: '2029-05-31',
    fitoutStart1: '2026-06-01',
    fitoutEnd1: '2026-07-31',
    fitoutStart2: '2027-06-01',
    fitoutEnd2: '2027-06-30',
    deliveryDate: '2026-05-31',
    rentPeriod1Start: '2026-06-01',
    rentPeriod1End: '2028-05-31',
    monthlyRent1: '11680',
    rentPeriod2Start: '2028-06-01',
    rentPeriod2End: '2029-05-31',
    monthlyRent2: '12264',
    propertyFee: '2880',
    firstRent: '35040',
    deposit: '35040',
    firstPayDate: '2026-05-09',
    depositPayDate: '2026-05-22',
    noticeAddress: '上海市宝山区罗店路388弄33号B座706室',
    taxPerSqm: '1250',
    riskChecks: ['面积前后一致', '价格口径一致', '免租期写清楚', '付款日期明确', '特殊条款需审批', '开票信息完整'],
  },
  floorplan: {
    requirements: ['最大化员工工位', '需要多个封闭会议室', '前台展示要宽敞'],
    floorplanNotes: '客户大约有40个工位需求，希望沿窗布置；老板办公室不需要太大，但会议室要有两个（一个10人，一个4人）。',
  },
  video: {
    videoCategory: '房源展示 / 带看实录',
    videoAction: '已发布：播放不错但无留资',
    videoTarget: ['豪华装修拎包入住', '适合科技研发'],
    videoInput: '视频发出去有5000多播放，转发也有30个，但后台私信就是没人问价，评论区有人说“看起来是不错，但我们小公司租不起”。',
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
    const status = key === 'demo' ? '安全断网' : (provider.configured ? provider.model : `${provider.model} / 演示`);
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
  if (field.type === 'contractBuilder') {
    const contractFields = [
      ['tenantName', '承租方', 'text', '例：星澜智能科技有限公司'],
      ['creditCode', '统一社会信用代码', 'text', '例：9131...'],
      ['registeredAddress', '注册地址', 'text', '例：上海市宝山区...'],
      ['legalRepresentative', '法定代表人', 'text', '例：陈星澜'],
      ['tenantPhone', '联系电话', 'text', '例：13800000000'],
      ['contactPerson', '联系人', 'text', '默认同法定代表人'],

      ['propertyAddress', '标的房屋地址', 'text', '例：上海市宝山区罗店路388弄33号B座706室'],
      ['roomCode', '房号', 'text', '例：B座706室'],
      ['area', '计租面积/㎡', 'number', '例：240'],
      ['unitPrice', '租金单价/元㎡天', 'number', '例：1.6'],
      ['propertyUnitFee', '物业费单价/元㎡月', 'number', '例：12'],
      ['leaseMonths', '租期', 'select', ''],
      ['leaseStart', '租期开始', 'date', ''],
      ['leaseEnd', '租期结束', 'date', ''],
      ['deliveryDate', '交付日期', 'date', ''],
      ['fitoutPattern', '装修期/月', 'select', ''],
      ['fitoutStart1', '装修期1开始', 'date', ''],
      ['fitoutEnd1', '装修期1结束', 'date', ''],
      ['fitoutStart2', '装修期2开始', 'date', ''],
      ['fitoutEnd2', '装修期2结束', 'date', ''],
      ['escalationRate', '末年递增/%', 'number', '例：5'],
      ['propertyFee', '物业费/月', 'number', '例：2880'],
      ['firstRentMonths', '首期租金/月数', 'select', ''],
      ['firstRent', '首期租金/元', 'number', '例：35040'],
      ['depositMonths', '押金/月数', 'select', ''],
      ['deposit', '保证金/元', 'number', '例：35040'],
      ['paymentCycle', '付款周期', 'select', ''],
      ['firstPayDate', '首期应缴日期', 'date', ''],
      ['depositPayDate', '保证金应缴日期', 'date', ''],
      ['noticeAddress', '承租方通知地址', 'text', '默认同房屋地址'],
      ['taxPerSqm', '纳税承诺/元每㎡', 'number', '例：1250'],
    ];
    const selectOptions = {
      leaseMonths: [
        ['24', '2年'],
        ['36', '3年'],
        ['60', '5年'],
      ],
      fitoutPattern: [
        ['0,0,0', '无装修期'],
        ['1,0,0', '1,0,0'],
        ['2,1,0', '2,1,0'],
        ['2,2,1', '2,2,1'],
        ['3,0,0', '3,0,0'],
        ['3,2,1', '3,2,1'],
        ['3,2,1', '3,2,1'],
      ],
      firstRentMonths: [
        ['1', '1个月'],
        ['2', '2个月'],
        ['3', '3个月'],
        ['4', '4个月'],
      ],
      depositMonths: [
        ['1', '1个月'],
        ['2', '2个月'],
        ['3', '3个月'],
        ['4', '4个月'],
      ],
      paymentCycle: [
        ['1', '1个月'],
        ['2', '2个月'],
        ['3', '季付'],
        ['6', '半年付'],
        ['12', '年付'],
      ],
    };
    return `
      <div class="contract-builder wide">
        <div class="contract-grid">
          ${contractFields.map(([key, label, type, placeholder]) => {
            const control = type === 'select'
              ? `<select data-key="${key}" data-type="select">${selectOptions[key].map(([value, text]) => `<option value="${value}">${text}</option>`).join('')}</select>`
              : `<input data-key="${key}" data-type="${type}" type="${type}" step="any" placeholder="${placeholder}">`;
            return `
              <label class="field-block">
                <span>${label}</span>
                ${control}
              </label>
            `;
          }).join('')}
        </div>
        <input data-key="rentPeriod1Start" data-type="text" type="hidden">
        <input data-key="rentPeriod1End" data-type="text" type="hidden">
        <input data-key="monthlyRent1" data-type="text" type="hidden">
        <input data-key="rentPeriod2Start" data-type="text" type="hidden">
        <input data-key="rentPeriod2End" data-type="text" type="hidden">
        <input data-key="monthlyRent2" data-type="text" type="hidden">
        <div class="contract-actions">
          <button id="contractPreviewBtn" class="secondary-btn" type="button">预览关键数字</button>
          <span id="contractStatus" class="muted-text">先预览关键数字，确认无误后在右侧生成合同。</span>
        </div>
      </div>
    `;
  }
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
  if (field.type === 'imageUploadHint') {
    return `
      <div class="field-block wide" style="background: var(--bg-100); padding: 1rem; border-radius: 8px; border: 1px dashed var(--border);">
        <p style="margin-top: 0; margin-bottom: 0.5rem; font-weight: 500;">📎 本地参考平面图</p>
        <p class="muted-text" style="font-size: 0.85rem; margin-bottom: 1rem;">当前AI仅生成文字版规划建议。为了建议更准确，请确保参考图具备：<strong>1. 办公室界限 2. 门窗位置 3. 承重墙与幕墙标记</strong>。您可在此载入图片方便对照填写下方说明。</p>
        <input type="file" id="localFloorplanUpload" accept="image/*" style="display: none;">
        <button type="button" class="secondary-btn" onclick="document.getElementById('localFloorplanUpload').click()">选择本地平面图预览</button>
        <div id="localFloorplanPreview" style="margin-top: 1rem; max-width: 100%; display: none;">
          <img src="" style="max-width: 100%; border-radius: 4px; border: 1px solid var(--border);">
        </div>
      </div>
    `;
  }
  if (field.type === 'designTools') {
    return `
      <div class="field-block wide" style="margin-top: 1rem; padding: 1.5rem; background: var(--bg-100); border-radius: 8px; text-align: center;">
        <p style="margin-bottom: 1rem; color: var(--text-secondary);">想要直接出3D效果图？请前往第三方专业工具：</p>
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
          <a href="https://jianzhuxuezhang.com/" target="_blank" class="primary-btn" style="text-decoration: none;">🚀 建筑学长 (AI出图)</a>
          <a href="https://www.kujiale.com/" target="_blank" class="secondary-btn" style="text-decoration: none;">酷家乐 (Coohom)</a>
          <a href="https://www.51jianmo.com/" target="_blank" class="secondary-btn" style="text-decoration: none;">51建模网</a>
        </div>
      </div>
    `;
  }
  if (field.type === 'transcriptUpload') {
    return `
      <div class="field-block wide">
        <span>上传录音文本</span>
        <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
          <input type="file" id="recapTranscriptUpload" accept=".txt,.md,.srt,.vtt,text/plain,text/markdown" style="display: none;">
          <button type="button" class="secondary-btn" id="recapTranscriptUploadBtn">选择转文字文本</button>
          <span id="recapUploadStatus" class="muted-text">支持 txt、md、srt、vtt；音频请先用转文字工具导出文本。</span>
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
  const generateBtn = document.querySelector('#generateBtn');
  const resultKicker = document.querySelector('.result-panel .step-kicker');
  const resultTitle = document.querySelector('.result-panel h2');
  const copyBtn = document.querySelector('#copyBtn');
  title.textContent = stage.title;
  desc.textContent = stage.desc;
  fields.innerHTML = stage.fields.map(fieldHtml).join('');
  if (generateBtn) {
    generateBtn.hidden = state.stage === 'contract';
  }
  if (resultKicker) resultKicker.textContent = state.stage === 'contract' ? '合同输出' : 'AI输出画布';
  if (resultTitle) {
    if (state.stage === 'contract') resultTitle.textContent = '合同文件';
    else if (state.stage === 'video') resultTitle.textContent = '短视频诊断与转化脚本';
    else if (state.stage === 'floorplan') resultTitle.textContent = '平面设计优化建议';
    else if (state.stage === 'pricing') resultTitle.textContent = '测算结果';
    else resultTitle.textContent = '灵感与策略建议';
  }
  if (copyBtn) copyBtn.hidden = state.stage === 'contract';
  if (state.stage === 'contract') {
    resultBox.dataset.raw = '';
    resultBox.innerHTML = '合同模块使用公司制式模板生成 Word 文件，不调用 AI。请填写并核对左侧字段后点击“生成合同下载”。';
  }
  renderNav();
  if (state.stage === 'pricing') {
    wirePricingSheet();
  }
  if (state.stage === 'contract') {
    wireContractBuilder();
  }
  if (state.stage === 'floorplan') {
    const uploadInput = document.getElementById('localFloorplanUpload');
    if (uploadInput) {
      uploadInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
          const url = URL.createObjectURL(file);
          const previewWrap = document.getElementById('localFloorplanPreview');
          const img = previewWrap.querySelector('img');
          img.src = url;
          previewWrap.style.display = 'block';
        }
      });
    }
  }
  if (state.stage === 'recap') {
    wireRecapTranscriptUpload();
  }
}

function wireRecapTranscriptUpload() {
  const uploadInput = document.querySelector('#recapTranscriptUpload');
  const uploadBtn = document.querySelector('#recapTranscriptUploadBtn');
  const status = document.querySelector('#recapUploadStatus');
  const transcript = fields.querySelector('[data-key="transcript"]');
  if (!uploadInput || !uploadBtn || !transcript) return;
  uploadBtn.addEventListener('click', () => uploadInput.click());
  uploadInput.addEventListener('change', () => {
    const file = uploadInput.files?.[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = () => {
      const text = String(reader.result || '').trim();
      const current = transcript.value.trim();
      transcript.value = current ? `${current}\n\n--- 上传文本：${file.name} ---\n${text}` : text;
      transcript.dispatchEvent(new Event('input', { bubbles: true }));
      if (status) status.textContent = `已载入：${file.name}`;
      uploadInput.value = '';
    };
    reader.onerror = () => {
      if (status) status.textContent = '读取失败，请复制文本后直接粘贴。';
    };
    reader.readAsText(file, 'UTF-8');
  });
}

function setPricingValue(key, value) {
  const node = fields.querySelector(`[data-key="${key}"]`);
  if (node) node.value = value ?? '';
}

function getPricingValue(key) {
  const node = fields.querySelector(`[data-key="${key}"]`);
  return node ? node.value : '';
}

function setContractValue(key, value) {
  const node = fields.querySelector(`[data-key="${key}"]`);
  if (node) node.value = value ?? '';
}

function getContractValue(key) {
  const node = fields.querySelector(`[data-key="${key}"]`);
  return node ? node.value : '';
}

function dateFromInput(value) {
  if (!value) return null;
  const date = new Date(`${value}T00:00:00`);
  return Number.isNaN(date.getTime()) ? null : date;
}

function formatInputDate(date) {
  if (!(date instanceof Date) || Number.isNaN(date.getTime())) return '';
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}

function addDays(date, days) {
  const next = new Date(date);
  next.setDate(next.getDate() + days);
  return next;
}

function addMonths(date, months) {
  const next = new Date(date);
  next.setMonth(next.getMonth() + months);
  return next;
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

function updateContractAutoFields(source = null) {
  const legal = getContractValue('legalRepresentative').trim();
  const contact = fields.querySelector('[data-key="contactPerson"]');
  if (contact && source?.dataset.key === 'legalRepresentative' && (contact.dataset.autoFilled === 'true' || contact.value.trim() === '')) {
    contact.value = legal;
    contact.dataset.autoFilled = 'true';
  }

  const propertyAddress = getContractValue('propertyAddress').trim();
  const notice = fields.querySelector('[data-key="noticeAddress"]');
  if (notice && source?.dataset.key === 'propertyAddress' && (notice.dataset.autoFilled === 'true' || notice.value.trim() === '')) {
    notice.value = propertyAddress;
    notice.dataset.autoFilled = 'true';
  }

  const leaseStart = dateFromInput(getContractValue('leaseStart'));
  const leaseMonths = Number(getContractValue('leaseMonths') || 36);
  if (leaseStart && leaseMonths > 0) {
    setContractValue('leaseEnd', formatInputDate(addDays(addMonths(leaseStart, leaseMonths), -1)));
    setContractValue('deliveryDate', formatInputDate(addDays(leaseStart, -1)));
    const paymentDate = formatInputDate(addDays(leaseStart, -10));
    setContractValue('firstPayDate', paymentDate);
    setContractValue('depositPayDate', paymentDate);
    setContractValue('rentPeriod1Start', formatInputDate(leaseStart));
    const phaseOneMonths = Math.max(12, leaseMonths - 12);
    const phaseTwoStart = addMonths(leaseStart, phaseOneMonths);
    setContractValue('rentPeriod1End', formatInputDate(addDays(phaseTwoStart, -1)));
    setContractValue('rentPeriod2Start', formatInputDate(phaseTwoStart));
    setContractValue('rentPeriod2End', formatInputDate(addDays(addMonths(leaseStart, leaseMonths), -1)));

    const pattern = getContractValue('fitoutPattern').split(',').map((item) => Number(item.trim() || 0));
    const firstFitoutMonths = pattern[0] || 0;
    const secondFitoutMonths = pattern[1] || 0;
    setContractValue('fitoutStart1', firstFitoutMonths > 0 ? formatInputDate(leaseStart) : '');
    setContractValue('fitoutEnd1', firstFitoutMonths > 0 ? formatInputDate(addDays(addMonths(leaseStart, firstFitoutMonths), -1)) : '');
    const secondStart = addMonths(leaseStart, 12);
    setContractValue('fitoutStart2', secondFitoutMonths > 0 ? formatInputDate(secondStart) : '');
    setContractValue('fitoutEnd2', secondFitoutMonths > 0 ? formatInputDate(addDays(addMonths(secondStart, secondFitoutMonths), -1)) : '');
  }

  const area = Number(getContractValue('area') || 0);
  const unitPrice = Number(getContractValue('unitPrice') || 0);
  const propertyUnitFee = Number(getContractValue('propertyUnitFee') || 0);
  const firstRentMonths = Number(getContractValue('firstRentMonths') || 3);
  const depositMonths = Number(getContractValue('depositMonths') || 3);
  const escalationRate = Number(getContractValue('escalationRate') || 0);
  const monthlyRent = Math.round(area * unitPrice * 365 / 12);
  const monthlyRent2 = Math.round(monthlyRent * (1 + escalationRate / 100));
  if (monthlyRent > 0) {
    setContractValue('monthlyRent1', String(monthlyRent));
    setContractValue('monthlyRent2', String(monthlyRent2));
    setContractValue('firstRent', String(monthlyRent * firstRentMonths));
    setContractValue('deposit', String(monthlyRent * depositMonths));
  }
  if (area > 0 && propertyUnitFee > 0) {
    setContractValue('propertyFee', String(Math.round(area * propertyUnitFee)));
  }
}

function wireContractBuilder() {
  // signYear removed
  setContractValue('leaseMonths', '36');
  setContractValue('fitoutPattern', '2,1,0');
  setContractValue('firstRentMonths', '3');
  setContractValue('depositMonths', '3');
  setContractValue('paymentCycle', '3');
  fields.querySelectorAll('[data-key]').forEach((node) => {
    if (node.dataset.key === 'contactPerson' || node.dataset.key === 'noticeAddress') {
      node.addEventListener('input', () => { node.dataset.autoFilled = 'false'; });
    }
    node.addEventListener('input', () => updateContractAutoFields(node));
    node.addEventListener('change', () => updateContractAutoFields(node));
  });
  const contact = fields.querySelector('[data-key="contactPerson"]');
  const notice = fields.querySelector('[data-key="noticeAddress"]');
  if (contact && contact.value.trim() !== '') contact.dataset.autoFilled = 'true';
  if (notice && notice.value.trim() !== '') notice.dataset.autoFilled = 'true';
  updateContractAutoFields();
  document.querySelector('#contractPreviewBtn')?.addEventListener('click', previewContractSummary);
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
  if (stageId === 'contract') {
    updateContractAutoFields();
  }
}

function loadDemoScenario() {
  applyStageValues(state.stage);
  resultBox.dataset.raw = '';
  resultBox.innerHTML = state.stage === 'contract'
    ? '已载入演示客户“星澜智能科技有限公司”。请核对合同字段后点击“生成合同下载”。'
    : '已载入演示客户“星澜智能科技有限公司”。现在可以直接点击“生成AI建议”；切换其他流程模块后，再点一次“载入演示客户”会填入对应模块的示例信息。';
}

function collectPayload() {
  const projectSelect = document.querySelector('#projectSelect');
  const project = {
    key: projectSelect.value,
    name: projectSelect.options[projectSelect.selectedIndex].textContent,
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
  return { csrf, stage: state.stage, provider, project, inputs };
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

let currentAbortController = null;

async function generate() {
  const button = document.querySelector('#generateBtn');
  const stopButton = document.querySelector('#stopBtn');
  button.disabled = true;
  button.textContent = '生成中...';
  if (stopButton) stopButton.style.display = 'inline-block';
  resultBox.innerHTML = '<p>正在整理业务信息并生成建议。</p>';

  if (currentAbortController) {
    currentAbortController.abort();
  }
  currentAbortController = new AbortController();

  try {
    if (state.stage === 'pricing') {
      await calculatePricing();
      return;
    }
    const res = await fetch('api/generate.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(collectPayload()),
      signal: currentAbortController.signal,
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
    if (error.name === 'AbortError') {
      resultBox.innerHTML += '<p class="demo-note" style="color: var(--text-secondary);">生成已主动停止。</p>';
    } else {
      resultBox.textContent = error.message;
    }
  } finally {
    button.disabled = false;
    button.textContent = '生成AI建议';
    if (stopButton) stopButton.style.display = 'none';
    currentAbortController = null;
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

async function generateContractDocx() {
  const button = document.querySelector('#contractDownloadBtn');
  if (!button) return;
  button.disabled = true;
  button.textContent = '生成中...';
  try {
    const res = await fetch('api/contract.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(collectPayload()),
    });
    const data = await res.json();
    if (!res.ok) throw new Error(data.error || '合同生成失败');
    resultBox.innerHTML += `<p class="demo-note">已生成：<a href="${escapeHtml(data.downloadUrl)}">${escapeHtml(data.filename)}</a></p>`;
  } catch (error) {
    resultBox.innerHTML += `<p class="form-error">${escapeHtml(error.message)}</p>`;
  } finally {
    button.disabled = false;
    button.textContent = '生成合同下载';
  }
}

async function previewContractSummary() {
  updateContractAutoFields();
  const payload = collectPayload();
  const data = payload.inputs;
  
  resultBox.innerHTML = '<p>正在生成关键数字与租金计划表预览...</p>';
  
  try {
    const res = await fetch('api/contract.php?preview=1', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });
    const previewData = await res.json();
    if (!res.ok) throw new Error(previewData.error || '获取租金明细表失败');
    
    const rows = previewData.rows || [];
    const rowsHtml = rows.map(row => `<tr>
      <td style="white-space: nowrap;">${escapeHtml(row[0])}</td>
      <td style="padding: 8px 4px; white-space: nowrap;">${escapeHtml(row[1])}</td>
      <td style="padding: 8px 0; color: var(--muted); text-align: center; width: 20px;">${escapeHtml(row[2])}</td>
      <td style="padding: 8px 4px; white-space: nowrap;">${escapeHtml(row[3])}</td>
      <td>${escapeHtml(row[4])}</td>
    </tr>`).join('');

    const summary = [
      ['承租方', data.tenantName],
      ['房屋地址', data.propertyAddress],
      ['计租面积', `${data.area || '-'} ㎡`],
      ['租期', `${data.leaseStart || '-'} 至 ${data.leaseEnd || '-'}（${data.leaseMonths || '-'}个月）`],
      ['交付日期', data.deliveryDate],
      ['装修期', `${data.fitoutPattern || '-'}；${data.fitoutStart1 || '-'} 至 ${data.fitoutEnd1 || '-'}；${data.fitoutStart2 || '-'} 至 ${data.fitoutEnd2 || '-'}`],
      ['首期/保证金应缴日期', `${data.firstPayDate || '-'} / ${data.depositPayDate || '-'}`],
      ['月租金', `${money(data.monthlyRent1)} 元；递增后 ${money(data.monthlyRent2)} 元`],
      ['物业费', `${money(data.propertyFee)} 元/月`],
      ['首期租金', `${money(data.firstRent)} 元（${data.firstRentMonths || '-'}个月）`],
      ['保证金', `${money(data.deposit)} 元（${data.depositMonths || '-'}个月）`],
      ['后续付款周期', `${data.paymentCycle || '-'}个月`],
      ['纳税承诺/元每㎡', data.taxPerSqm || '-'],
    ];
    resultBox.dataset.raw = summary.map(([label, value]) => `${label}: ${value}`).join('\n');
    resultBox.innerHTML = `
      <h3>合同关键数字预览</h3>
      <table class="excel-table output-table">
        <tbody>
          ${summary.map(([label, value]) => `<tr><td>${escapeHtml(label)}</td><td>${escapeHtml(value)}</td></tr>`).join('')}
        </tbody>
      </table>
      
      <h4>租金计划表明细预览</h4>
      <div style="overflow-x: auto;">
        <table class="excel-table output-table" style="min-width: 480px;">
          <thead>
            <tr>
              <th>应缴日期</th>
              <th colspan="3" style="text-align: center;">租期</th>
              <th>应缴金额</th>
            </tr>
          </thead>
          <tbody>
            ${rowsHtml}
          </tbody>
        </table>
      </div>

      <p style="margin-top: 1rem;">请核对左侧字段和上方关键数字及租金明细。确认无误后生成公司制式合同。</p>
      <button id="contractDownloadBtn" class="primary-btn" type="button">生成合同下载</button>
    `;
    document.querySelector('#contractDownloadBtn')?.addEventListener('click', generateContractDocx);
  } catch (error) {
    resultBox.innerHTML = `<p class="form-error">${escapeHtml(error.message)}</p>`;
  }
}

nav.addEventListener('click', (event) => {
  const button = event.target.closest('[data-stage]');
  if (!button) return;
  state.stage = button.dataset.stage;
  renderStage();
});

document.querySelector('#generateBtn').addEventListener('click', generate);
const stopBtn = document.querySelector('#stopBtn');
if (stopBtn) {
  stopBtn.addEventListener('click', () => {
    if (currentAbortController) {
      currentAbortController.abort();
    }
  });
}
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
