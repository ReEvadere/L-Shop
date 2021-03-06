<template>
    <v-layout align-center justify-center v-if="product !== null">
        <v-flex xs12 sm12 md10 lg8>
            <v-card>
                <v-card-title class="text-xs-center">
                    <h3>{{ $t('content.admin.products.edit.title') }}</h3>
                    <v-spacer></v-spacer>
                    <span class="caption" v-html="$t('common.unclear_docs', {link: 'https://github.com/D3lph1/L-shop/wiki'})"></span>
                </v-card-title>
                <v-card-text>
                    <v-select
                            :items="items"
                            item-text="name"
                            item-value="id"
                            :return-object="true"
                            v-model="item"
                            :label="$t('content.admin.products.add.item')"
                            :prepend-icon="icon"
                    >
                        <template slot="item" slot-scope="data">
                            <v-list-tile-avatar :tile="true" size="35">
                                <img :src="data.item.image" class="br0">
                            </v-list-tile-avatar>
                            <v-list-tile-content>
                                <v-list-tile-title>
                                    <span v-html="data.item.name"></span>
                                    <v-enchanted v-if="data.item.enchanted"></v-enchanted>
                                </v-list-tile-title>
                            </v-list-tile-content>
                        </template>
                    </v-select>
                    <v-select
                            :items="servers"
                            item-text="name"
                            item-value="id"
                            :return-object="true"
                            v-model="server"
                            :rules="[validateCategories]"
                            :label="$t('content.admin.products.add.server')"
                            prepend-icon="storage"
                    >
                    </v-select>
                    <v-select
                            v-show="server !== null && server.categories.length !== 0"
                            :items="categories"
                            item-text="name"
                            item-value="id"
                            :return-object="true"
                            v-model="category"
                            :label="$t('content.admin.products.add.category')"
                            prepend-icon="tab"
                    >
                    </v-select>
                    <v-text-field
                            type="number"
                            class="mt-4 no-spinners"
                            v-show="isItem && category && this.server.categories.length !== 0"
                            :label="$t('content.admin.products.add.item_stack')"
                            v-model="amount"
                            prepend-icon="plus_one"
                    ></v-text-field>
                    <v-text-field
                            type="number"
                            class="mt-4 no-spinners"
                            v-show="isCurrency && category && this.server.categories.length !== 0"
                            :label="$t('content.admin.products.add.currency_stack')"
                            v-model="amount"
                            prepend-icon="plus_one"
                    ></v-text-field>
                    <v-switch
                            v-show="isPermgroup && category && this.server.categories.length !== 0"
                            color="secondary"
                            class="mt-4"
                            :label="$t('content.admin.products.add.forever')"
                            v-model="forever"
                    ></v-switch>
                    <v-text-field
                            type="number"
                            class="no-spinners"
                            v-show="isPermgroup && category && this.server.categories.length !== 0"
                            :label="$t('content.admin.products.add.permgroup_stack')"
                            :disabled="forever"
                            v-model="amount"
                            prepend-icon="timelapse"
                    ></v-text-field>
                    <v-text-field
                            v-if="category"
                            type="number"
                            class="no-spinners"
                            :label="priceLabel"
                            v-model="price"
                            prepend-icon="attach_money"
                    ></v-text-field>
                    <v-text-field
                            type="number"
                            class="no-spinners"
                            :label="$t('content.admin.products.add.sort_priority')"
                            v-model="sortPriority"
                            prepend-icon="sort"
                    ></v-text-field>
                    <v-switch
                            color="secondary"
                            :label="$t('content.admin.products.add.hide')"
                            v-model="hidden"
                    ></v-switch>
                </v-card-text>
                <v-card-actions>
                    <v-btn
                            flat
                            color="orange"
                            :disabled="finishDisabled"
                            :loading="finishLoading"
                            @click="perform"
                    >
                        {{ $t('content.admin.products.edit.finish') }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-flex>
    </v-layout>
</template>

<script>
    import loader from './../../../core/http/loader'

    export default {
        data() {
            return {
                item: null,
                items: [],
                server: null,
                servers: [],
                category: null,
                categories: [],
                product: null,
                isItem: false,
                isPermgroup: false,
                isCurrency: false,
                isRegionOwner: false,
                isRegionMember: false,
                isCommand: false,

                forever: false,
                amount: 0,
                price: 0,
                sortPriority: 0,
                hidden: false,
                finishLoading: false
            }
        },
        beforeRouteEnter (to, from, next) {
            loader.beforeRouteEnter(`/spa/admin/products/edit/${to.params.product}`, to, from, next);
        },
        beforeRouteUpdate (to, from, next) {
            loader.beforeRouteUpdate(`/spa/admin/products/edit/${to.params.product}`, to, from, next, this);
        },
        watch: {
            item(val) {
                if (val !== null) {
                    this.isItem = val.type.isItem;
                    this.isPermgroup = val.type.isPermgroup;
                    this.isCurrency = val.type.isCurrency;
                    this.isRegionOwner = val.type.isRegionOwner;
                    this.isRegionMember = val.type.isRegionMember;
                    this.isCommand = val.type.isCommand;
                }
            },
            server(val) {
                if (val !== null) {
                    this.categories = val.categories;
                }
            }
        },
        computed: {
            icon() {
                if (this.isItem) {
                    return 'beach_access';
                }

                if (this.isPermgroup) {
                    return 'turned_in_not';
                }

                if (this.isCurrency) {
                    return 'monetization_on';
                }

                if (this.isRegionOwner) {
                    return 'supervisor_account';
                }

                if (this.isRegionMember) {
                    return 'person';
                }

                if (this.isCommand) {
                    return 'keyboard_arrow_right';
                }
            },
            priceLabel() {
                if (this.isItem) {
                    return $t('content.admin.products.add.price_for_stack');
                }

                if (this.isPermgroup) {
                    return $t('content.admin.products.add.price_for_period');
                }

                if (this.isCurrency) {
                    return $t('content.admin.products.add.price_for_currency');
                }

                if (this.isRegionOwner || this.isRegionMember) {
                    return $t('content.admin.products.add.price_for_region');
                }

                if (this.isCommand) {
                    return $t('content.admin.products.add.price_for_command');
                }
            },
            finishDisabled() {
                return this.item === null || this.item === '' ||
                    this.server === null || this.server === '' ||
                    this.category === null || this.category === '' ||
                    (this.isPermgroup && !this.forever && Number(this.amount) <= 0) ||
                    (this.isItem && Number(this.amount) <= 0) ||
                    Number(this.price) <= 0 ||
                    this.server === null || this.server.categories.length === 0;
            }
        },
        methods: {
            validateCategories() {
                if (this.server !== null && this.server.categories.length === 0) {
                    return $t('content.admin.products.add.no_categories');
                }

                return true;
            },
            perform() {
                this.finishLoading = true;

                this.$axios.post(`/spa/admin/products/edit/${this.product.id}`, {
                    item: this.item.id,
                    category: this.category.id,
                    stack: this.amount,
                    forever: this.forever,
                    price: this.price,
                    sort_priority: this.sortPriority,
                    hidden: this.hidden
                })
                    .then(response => {
                        if (response.data.status === 'success') {
                            this.$router.push({name: 'admin.products.list'});
                        } else {
                            this.finishLoading = false;
                        }
                    });
            },
            setData(response) {
                const data = response.data;

                data.items.forEach(product => {
                    if (product.id === data.product.item.id) {
                        this.item = product;
                    }
                });
                data.servers.forEach(server => {
                    server.categories.forEach(category => {
                        if (category.id === data.product.category.id) {
                            this.server = server;
                            this.category = category;
                        }
                    });
                });

                this.items = data.items;
                this.servers = data.servers;
                this.product = data.product;
                this.isItem = data.product.item.type.isItem;
                this.isPermgroup = data.product.item.type.isPermgroup;
                this.isCurrency = data.product.item.type.isCurrency;
                this.isRegionOwner = data.product.item.type.isRegionOwner;
                this.isRegionMember = data.product.item.type.isRegionMember;
                this.isCommand = data.product.item.type.isCommand;
                this.forever = data.product.stack === 0;
                this.amount = data.product.stack;
                this.price = data.product.price;
                this.sortPriority = data.product.sort_priority;
                this.hidden = data.product.hidden;
            }
        }
    }
</script>
