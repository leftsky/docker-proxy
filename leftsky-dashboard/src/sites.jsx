import React, { PureComponent } from 'react'
import _ from "loadsh";
import styles from './sites.module.scss'
import wsapi from './websocket-api'

export default class sites extends PureComponent {
  state = {
    sites: []
  }

  componentDidMount() {

  }

  componentWillUnmount() {
    setTimeout(() => {
      this.getBlogs();
    }, 500);
  }

  getBlogs() {
    wsapi.sendCmd("getBlogs", {}).then(res => {
      console.log(res)
      this.setState({
        sites: res
      })
    })
  }

  render() {
    const { sites } = this.state;
    return (
      <div className={styles.boxes}>
        {_.map(sites, item => {
          return <div className={styles.boxItem}>
            <div>名称：{item.name}</div>
            <div>IP: {item.ip}</div>
            <div>SLL证书状态：{item.ssl_status}</div>
          </div>
        })}
      </div>
    )
  }
}
